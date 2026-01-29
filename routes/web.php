<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\Http;
use App\Mail\QualifiedForEnrollment;
use App\Mail\StudentVerificationMail;
use Carbon\Carbon;


// Public routes
Route::get('/', function () {
    return view('welcome');
});

// Student Routes
Route::prefix('/exe')->group(function (){
    Route::post('/student', [StudentController::class, 'register']);
});

// Show verification form (already exists)
Route::get('/register_student_account', function () {
    $email = session('registration_email');
    $registerData = $email ? Cache::get('registration_' . $email) : null;
    
    if (!$registerData) {
        return redirect('/')->with('error', 'Verification session expired. Please register again.');
    }

    // This ensures the server always sends attempts = 0 for new registrations
    if ($registerData['attempts'] >= 3) {
        // Clear the old cache and redirect
        Cache::forget('registration_' . $email);
        session()->forget('registration_email');
        return redirect('/')->with('error', 'Previous registration expired. Please register again.');
    }
    
    return view('student-verify', [
        'email' => $email,
        'registerData' => $registerData
    ]);
})->name('student.verification');

// Verify OTP
Route::post('/verify-student-otp', function (Illuminate\Http\Request $request) {
    $request->validate([
        'email' => 'required|email',
        'otp' => 'required|digits:6',
    ]);
    
    $email = $request->email;
    $cacheKey = 'registration_' . $email;
    $registerData = Cache::get($cacheKey);
    
    if (!$registerData) {
        return redirect('/')->with('error', 'Verification session expired. Please register again.');
    }
    
    // Check if attempts exceeded
    if ($registerData['attempts'] >= 3) {
        Cache::forget($cacheKey);
        session()->forget('registration_email');
        return redirect('/')->with('error', 'Too many failed attempts. Please register again.');
    }
    
    // Check OTP
    if ($request->otp != $registerData['otp']) {
        // Increment attempts
        $registerData['attempts']++;
        Cache::put($cacheKey, $registerData, now()->addMinutes(1));
        
        $remainingAttempts = 3 - $registerData['attempts'];
        return response()->json([
            'success' => false,
            'message' => $remainingAttempts > 0 
                ? "Invalid OTP. {$remainingAttempts} attempt" . ($remainingAttempts > 1 ? 's' : '') . " remaining."
                : 'Invalid OTP.'
        ], 400);
    }
    
    // OTP is correct - Create student account
    try {
        // Import Student model if not already imported
        // use App\Models\Student;
        $currentDate = now()->toDateTimeString();

        $user = new App\Models\User();
        $user->id = $registerData['id'];
        $user->email2 = $registerData['email'];
        $user->password = Hash::make($registerData['password']);
        $user->profile = 'default.png';
        $user->date_created = $currentDate;
        $user->user_type = 'student';
        $user->is_active = 1;
        $user->save();

        $userInfo = new App\Models\UserInfo();
        $userInfo->user_id = $registerData['id'];
        $userInfo->firstname = $registerData['firstName'];
        $userInfo->lastname = $registerData['lastName'];
        $userInfo->middlename = $registerData['middlename'];
        $userInfo->birthdate = $registerData['bdate'];
        $userInfo->age = $registerData['age'];
        $userInfo->sex = $registerData['sex'];
        $userInfo->relationship_status = $registerData['relationship_status'];
        $userInfo->save();

        $address = new App\Models\Address();
        $address->user_id = $registerData['id'];
        $address->street_no = $registerData['houseStreet'];
        $address->region = $registerData['region'];
        $address->province = $registerData['province'];
        $address->municipality = $registerData['municipality'];
        $address->brgy = $registerData['barangay'];
        $address->zipcode = $registerData['zip_code'];
        $address->save();


            date_default_timezone_set('Asia/Manila');
            $todays_date=date("Y-m-d h:i:sa");
            $today=strtotime($todays_date);
            $date=date("Y-m-d h:i:sa", $today);


            // Get current date for SY calculation
            $currentYear = date('Y');
            $currentMonth = date('n'); // 1-12
            
            // Calculate School Year and Semester
            if ($currentMonth >= 7 && $currentMonth <= 12) {
                // July to December: First Semester of current school year
                $schoolYear = $currentYear . '-' . ($currentYear + 1);

            } else {
                // January to June: Second Semester of previous school year
                $schoolYear = ($currentYear - 1) . '-' . $currentYear;
            }

        $student = new App\Models\Student();
        $student->student_id = $userInfo->id;
        $student->id_no = $registerData['studentId'];
        $student->year_level = $registerData['year_level'];
        $student->status = 'Not Enrolled';
        $student->curriculum = $registerData['curriculum'];
        $student->is_regular = $registerData['is_regular'];
        $student->enrolled = 0;
        $student->sy = $schoolYear;
        $student->save();
        
        // Clear cache and session
        Cache::forget($cacheKey);
        session()->forget('registration_email');
        
        // Optionally log the student in
        // Auth::login($student);
        
        return response()->json([
            'success' => true,
            'message' => 'Registration successful! Your account has been created.',
            'redirect' => '/login' // Redirect to login page
        ]);
        
    } catch (\Exception $e) {
        return redirect('/')->with('error', 'Failed to create account. Please try again.');
    }
})->name('verify-student-otp');

// Add this to your web.php routes
Route::post('/api/clear-lockout-cache', function (Illuminate\Http\Request $request) {
    $request->validate([
        'email' => 'required|email'
    ]);
    
    $cacheKey = 'registration_' . $request->email;
    Cache::forget($cacheKey);
    session()->forget('registration_email');
    
    return response()->json([
        'success' => true,
        'message' => 'Cache cleared',
        'redirect' => '/'
    ]);
});

// Resend OTP
Route::post('/resend-student-otp', function (Illuminate\Http\Request $request) {
    $request->validate([
        'email' => 'required|email',
    ]);
    
    $email = $request->email;
    $cacheKey = 'registration_' . $email;
    $registerData = Cache::get($cacheKey);
    
    if (!$registerData) {
        return response()->json([
            'success' => false,
            'message' => 'Session expired. Please register again.'
        ], 400);
    }
    
    // Generate new OTP
    $newOtp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    
    // Update cache with new OTP and reset attempts if requested
    $registerData['otp'] = $newOtp;
    if ($request->reset_attempts) {
        $registerData['attempts'] = 0;
    }
    
    Cache::put($cacheKey, $registerData, now()->addMinutes(1));
    
    // Send new OTP via email (implement your email sending logic here)
    // Mail::to($email)->send(new StudentVerificationMail($newOtp));
    
    // For demo purposes, log the OTP
    Log::info("New OTP for {$email}: {$newOtp}");
    
    return response()->json([
        'success' => true,
        'message' => 'New verification code sent to your email.'
    ]);
})->name('resend-student-otp');