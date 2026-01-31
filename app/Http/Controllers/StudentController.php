<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Models\UserInfo;
use App\Models\Student;
use App\Models\Subject;
use App\Models\SubjectSchedule;
use App\Mail\RegistrationVerification;
use App\Mail\SendQuestion;
use Illuminate\Support\Facades\Log;
use App\Mail\PasswordResetOtp;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use DateTime;

class StudentController extends Controller
{
    public function register(Request $request)
    {
        $action = $request->input('action');

        switch ($action) {

            case 'send_registration':
                return $this->sendVerificationCode($request);

            default:
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid action'
                ]);
        }
    }

    
    private function sendVerificationCode(Request $request)
    {
        try {
            // Validate the registration data
            $validator = Validator::make($request->all(), [
                'studentId' => [
                    'required',
                    function ($attribute, $value, $fail) {
                        try {
                            // Check if student number contains a dash
                            if (strpos($value, '-') === false) {
                                // $fail('The student number must contain a dash (e.g., 2020-30617).');
                                $fail('Invalid Student Number');
                                return;
                            }
                            
                            // Split the student number into year and number parts
                            $parts = explode('-', $value);
                            
                            if (count($parts) !== 2) {
                                // $fail('Invalid student number format. Use format: YYYY-NNNNN.');
                                $fail('Invalid Student Number');
                                return;
                            }
                            
                            $year = $parts[0];
                            $number = $parts[1];
                            
                            // Check if year is numeric and within range
                            if (!is_numeric($year) || strlen($year) !== 4) {
                                $fail('Invalid Student Number');
                                // $fail('The year part must be a 4-digit number.');
                                return;
                            }
                            
                            $year = (int)$year;
                            if ($year < 2013 || $year > 2050) {
                                $fail('Invalid Student Number');
                                // $fail('The year must be between 2013 and 2050.');
                                return;
                            }
                            
                            // Check if number part is valid
                            if (!is_numeric($number) || strlen($number) !== 5) {
                                // $fail('The number part must be a 5-digit number.');
                                $fail('Invalid Student Number');
                                return;
                            }
                            
                            // Check if student number already exists in database
                            if (DB::table('students')->where('id_no', $value)->exists()) {
                                $fail('This student number already exists.');
                                return;
                            }
                            
                        } catch (\Exception $e) {
                            $fail('Invalid student number format.');
                        }
                    }
                ],
                'email' => [
                    'required',
                    'email'
                ],
                'password' => [
                    'required',
                    'min:8'
                ],
                'bdate' => [
                    'required',
                    'date',
                    function ($attribute, $value, $fail) {
                        try {
                            $bdate = Carbon::parse($value);
                            $today = Carbon::now();
                            $age = $bdate->diffInYears($today);
                            
                            // Check if born in 2018 or earlier
                            $birthYear = $bdate->year;
                            
                            if ($birthYear > 2008) {
                                // $fail('You must be born in 2018 or earlier to register.');
                                $fail('Input your real birthdate');
                            }
                            
                            // Check minimum age of 6 years
                            if ($age < 17) {
                                // $fail('You must be at least 17 years old to register.');
                                $fail('Input your real birthdate');
                            }
                        } catch (\Exception $e) {
                            $fail('Invalid date format.');
                        }
                    }
                ],
                'sex' => [
                    'required'
                ],
                'status' => [
                    'required'
                ],
                'houseStreet' => [
                    'required'
                ],
                'region' => [
                    'required'
                ],
                'province' => [
                    'required'
                ],
                'municipality' => [
                    'required'
                ],
                'barangay' => [
                    'required'
                ],
                'zipcode' => [
                    'required'
                ]
            ], [
                // Custom messages if needed
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ]);
            }

            // Calculate and store age for later use
            $bdate = Carbon::parse($request->bdate);
            $age = $bdate->diffInYears(Carbon::now());

            // First, extract the year from the student number
            $studentYear = (int) explode('-', $request->studentId)[0]; // Gets "2020" from "2020-30617"

            // Get all active curricula ordered by year (descending = newest first)
            $activeCurricula = DB::table('curriculum')
                                ->where('is_active', 1)
                                ->orderBy('curriculum_year', 'desc')
                                ->get();

            $assignedCurriculum = null;

            // Find the appropriate curriculum
            foreach ($activeCurricula as $curriculum) {
                if ($studentYear >= (int)$curriculum->curriculum_year) {
                    $assignedCurriculum = $curriculum->curriculum_year;
                    break;
                }
            }

            // If no curriculum found (student year is before all active curricula), use the oldest active one
            if (!$assignedCurriculum && $activeCurricula->isNotEmpty()) {
                $assignedCurriculum = $activeCurricula->last()->curriculum_year;
            }


            $find = DB::table('csv')
                    ->where('email', $request->email)
                    ->exists();

            // If email doesn't exist in CSV, check if it's a valid evsu.edu.ph email
            if (!$find) {
                // Check if it's an evsu.edu.ph email
                if (!preg_match('/^[^\s@]+@evsu\.edu\.ph$/i', $request->email)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Email not found in our records.'
                    ]);
                }
                
               // Check if email already exists
                $emailExists = User::where('email2', $request->email)->exists();

                if ($emailExists) {
                    return response()->json([
                        'success' => false,
                        'message' => 'This email is already registered!'
                    ]);
                }


                // Generate verification code (6 digits)
                $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

                $studentId = $this->generateUniqueStudentId();

                Cache::put('registration_' . $request->email, [
                    'otp' => $verificationCode,
                    'id' => $studentId,
                    'firstName' => $request->firstName,
                    'lastName' => $request->lastName,
                    'middlename' => $request->middlename,
                    'studentId' => $request->studentId,
                    'password' => $request->password,
                    'email' => $request->email,
                    'bdate' => $request->bdate,
                    'age' => $age, // Store calculated age
                    'sex' => $request->sex,
                    'houseStreet' => $request->houseStreet,
                    'region' => $request->region,
                    'province' => $request->province,
                    'municipality' => $request->municipality,
                    'barangay' => $request->barangay,
                    'zip_code' => $request->zipcode,
                    'relationship_status' => $request->status,
                    'is_regular' => '2',
                    'year_level' => "NONE",
                    'curriculum' => $assignedCurriculum,
                    'attempts' => 0
                ], now()->addMinutes(1));

                session(['registration_email' => $request->email]);

                // Send verification email
                try {
                    Mail::to($request->email)->send(new RegistrationVerification($verificationCode, $request->firstName, $request->lastName));

                    // Check if email was actually sent
                    if (count(Mail::failures()) > 0) {
                        Log::error('Email failed to send to: ' . $request->email);
                        return response()->json([
                            'success' => false,
                            'message' => 'Failed to send verification email. Please try again.'
                        ]);
                    }

                    Log::info('Verification code sent successfully to: ' . $request->email);

                    return response()->json([
                        'success' => true,
                        'message' => 'Verification code sent to your email!',
                        'email' => $request->email,
                    ]);

                } catch (\Exception $e) {
                    Log::error('Failed to send verification email: ' . $e->getMessage());
                    Log::error('Email error details: ', ['exception' => $e]);
                    
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to send verification email: ' . $e->getMessage()
                    ]);
                }
                
            } else {

                // Check if email already exists
                $emailExists = User::where('email2', $request->email)->exists();

                if ($emailExists) {
                    return response()->json([
                        'success' => false,
                        'message' => 'This email is already registered!'
                    ]);
                }

                // Generate verification code (6 digits)
                $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

                $studentId = $this->generateUniqueStudentId();

                Cache::put('registration_' . $request->email, [
                    'otp' => $verificationCode,
                    'id' => $studentId,
                    'studentId' => $request->studentId,
                    'firstName' => $request->firstName,
                    'lastName' => $request->lastName,
                    'middlename' => $request->middlename,
                    'password' => $request->password,
                    'email' => $request->email,
                    'bdate' => $request->bdate,
                    'age' => $age, // Store calculated age
                    'sex' => $request->sex,
                    'houseStreet' => $request->houseStreet,
                    'region' => $request->region,
                    'province' => $request->province,
                    'municipality' => $request->municipality,
                    'barangay' => $request->barangay,
                    'zip_code' => $request->zipcode,
                    'relationship_status' => $request->status,
                    'is_regular' => '1',
                    'year_level' => "1st Year",
                    'curriculum' => $assignedCurriculum,
                    'attempts' => 0
                ], now()->addMinutes(1));

                session(['registration_email' => $request->email]);

                // Send verification email
                try {
                    Mail::to($request->email)->send(new RegistrationVerification($verificationCode, $request->firstName, $request->lastName));

                    // Check if email was actually sent
                    if (count(Mail::failures()) > 0) {
                        Log::error('Email failed to send to: ' . $request->email);
                        return response()->json([
                            'success' => false,
                            'message' => 'Failed to send verification email. Please try again.'
                        ]);
                    }

                    Log::info('Verification code sent successfully to: ' . $request->email);

                    return response()->json([
                        'success' => true,
                        'message' => 'Verification code sent to your email!',
                        'email' => $request->email,
                    ]);

                } catch (\Exception $e) {
                    Log::error('Failed to send verification email: ' . $e->getMessage());
                    Log::error('Email error details: ', ['exception' => $e]);
                    
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to send verification email: ' . $e->getMessage()
                    ]);
                }

                // ------ else
            }


        } catch (\Exception $e) {
            Log::error('Send verification error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }


    private function generateUniqueStudentId()
    {
        $maxAttempts = 10;
        $attempt = 0;

        do {
            $studentId = mt_rand(100000, 999999);
            $exists = User::where('id', $studentId)->exists();
            $attempt++;

            if ($attempt >= $maxAttempts) {
                throw new \Exception('Unable to generate unique student ID after ' . $maxAttempts . ' attempts');
            }
        } while ($exists);

        return $studentId;
    }

    // LOGIN
    /**
     * Handle student login
     */
    public function login(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if user exists
        $user = User::where('email2', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password'
            ], 401);
        }

        // Check if user is active
        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Your account is deactivated. Please contact administrator.'
            ], 401);
        }

        // Check if user is a student
        if ($user->user_type !== 'student') {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Only students can login here.'
            ], 403);
        }

        // Verify password
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password'
            ], 401);
        }

        // LOG THE USER INTO THE SESSION
        Auth::guard('student')->login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        // Update last login
        $user->last_login = Carbon::now();
        $user->save();

        // Prepare response data
        // $responseData = [
        //     'success' => true,
        //     'message' => 'Login successful!',
        //     'redirect' => '/dashboard', // Change this to your dashboard route
        //     'user' => [
        //         'id' => $user->id,
        //         'email' => $user->email2,
        //         'type' => $user->user_type,
        //         'profile' => $user->profile,
        //         'student_info' => $user->user_information
        //     ]
        // ];
        $responseData = [
            'success' => true,
            'message' => 'Login successful!'
        ];


        return response()->json($responseData);
    }

    /**
     * Handle student logout
     */
    public function logout(Request $request)
    {
        Auth::guard('student')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    public function dashboard(Request $request)
    {
        if (!Auth::guard('student')->check()) {
            return redirect('/')->with('error', 'Please login first.');
        }

        $user = Auth::guard('student')->user();

        // Get current date for SY and SEM calculation
        $currentYear = date('Y');
        $currentMonth = date('n'); // 1-12
        
        // Calculate School Year and Semester
        if ($currentMonth >= 7 && $currentMonth <= 12) {
            // July to December: First Semester of current school year
            $schoolYear = $currentYear . '-' . ($currentYear + 1);
            $semester = 'SEM 1';
        } else {
            // January to June: Second Semester of previous school year
            $schoolYear = ($currentYear - 1) . '-' . $currentYear;
            $semester = 'SEM 2';
        }
        
        $pageTitle = "SY: $schoolYear $semester";

        $sem = $semester;

        return view('student.dashboard', compact(
            'user', 
            'pageTitle',
            'sem'
        ));

    }

    /**
     * Get authenticated user
     */
    public function user(Request $request)
    {
        $user = Auth::guard('student')->user();
        
        if ($user) {
            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email2,
                    'type' => $user->user_type,
                    'profile' => $user->profile,
                    'full_name' => $user->user_information ? 
                        $user->user_information->firstname . ' ' . $user->user_information->lastname : null,
                    'student_id' => $user->student ? $user->student->id_no : null
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Not authenticated'
        ], 401);
    }

    /**
     * Handle forgot password
     */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please enter a valid email address'
            ], 422);
        }

        $user = User::where('email2', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Email not found in our system'
            ], 404);
        }

        // Here you would typically:
        // 1. Generate a password reset token
        // 2. Send email with reset link
        // 3. Return success message

        // For now, just return success
        return response()->json([
            'success' => true,
            'message' => 'Password reset instructions have been sent to your email.'
        ]);
    }

}
