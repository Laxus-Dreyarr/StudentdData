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
use App\Models\SubjectSchedule;
use App\Models\EnrolledSubjects;
use App\Models\Subject;
use App\Models\Curriculum;
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
        // Check if user is logged in
        if (Auth::guard('student')->check()) {
            // Get the current session ID before logout (for debugging)
            $sessionId = $request->session()->getId();
            Log::info("Logging out student. Session ID: " . $sessionId);
            
            // Logout the student
            Auth::guard('student')->logout();
            
            // Flush all session data
            $request->session()->flush();
            
            // Invalidate the session
            $request->session()->invalidate();
            
            // Regenerate the CSRF token
            $request->session()->regenerateToken();
            
            // Clear all session cookies
            $cookieNames = [
                config('session.cookie'), // Default: 'enrollsys_studentdata_session'
                'XSRF-TOKEN',
            ];
            
            $response = null;
            
            // Return JSON response for AJAX
            if ($request->ajax()) {
                $response = response()->json([
                    'success' => true,
                    'message' => 'Logged out successfully',
                    'redirect' => '/'
                ]);
            } else {
                // Redirect to login page
                $response = redirect('/')->with('success', 'Logged out successfully');
            }
            
            // Clear each cookie
            foreach ($cookieNames as $cookieName) {
                $response->withCookie(cookie()->forget($cookieName));
            }
            
            // Also try to expire the session cookie with proper path
            $sessionName = config('session.cookie');
            $response->withCookie(cookie(
                $sessionName,
                null,
                -1,
                config('session.path'),
                config('session.domain'),
                config('session.secure'),
                config('session.http_only'),
                false,
                config('session.same_site')
            ));
            
            return $response;
        }
        
        return response()->json([
            'success' => false,
            'message' => 'No user logged in'
        ], 401);
    }

    public function dashboard(Request $request)
    {
        if (!Auth::guard('student')->check()) {
            return redirect('/')->with('error', 'Please login first.');
        }

        
        $user = Auth::guard('student')->user();
        $student = $user->user_information->student;
        $studentID = $student->id;

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

        // Check if student has enrolled subjects
        $hasEnrolledSubjects = EnrolledSubjects::where('student_id', $student->id)->exists();

        // Initialize variables for dashboard stats
        $gwa = 0;
        $totalSubjects = 0;
        $totalUnits = 0;
        $enrolledSubjects = collect();

        // If student has enrolled subjects, calculate real data
        if ($hasEnrolledSubjects) {
            // Get enrolled subjects with subject details
            $enrolledSubjects = EnrolledSubjects::where('student_id', $student->id)
                ->with(['subject' => function($query) {
                    $query->select('id', 'code', 'name', 'units');
                }])
                ->get();
            
            // Calculate statistics
            $totalSubjects = $enrolledSubjects->count();
            $totalGradePoints = 0;
            
            foreach ($enrolledSubjects as $enrolled) {
                if ($enrolled->subject) {
                    $units = $enrolled->subject->units ?? 3;
                    $totalUnits += $units;
                    
                    // Calculate grade points
                    $numericGrade = $this->convertGradeToNumeric($enrolled->grade);
                    $totalGradePoints += ($numericGrade * $units);
                }
            }
            
            // Calculate GWA
            if ($totalUnits > 0) {
                $gwa = $totalGradePoints / $totalUnits;
            }
        }
        
        // If no enrolled subjects, get available subjects for their curriculum
        $availableSubjects = [];
        if (!$hasEnrolledSubjects) {
            // Get curriculum_id from curriculum table based on student's curriculum year
            $curriculum = Curriculum::where('curriculum_year', $student->curriculum)->first();
            
            if ($curriculum) {
                // Get all subjects for this curriculum (for search functionality)
                $allSubjects = Subject::where('curriculum_id', $curriculum->id)
                    ->where('is_active', 1)
                    ->orderByRaw("
                        CASE year_level 
                            WHEN '1st Year' THEN 1
                            WHEN '2nd Year' THEN 2
                            WHEN '3rd Year' THEN 3
                            WHEN '4th Year' THEN 4
                            WHEN '5th Year' THEN 5
                            ELSE 6
                        END,
                        CASE semester
                            WHEN '1st Sem' THEN 1
                            WHEN '2nd Sem' THEN 2
                            WHEN 'Summer' THEN 3
                            ELSE 4
                        END,
                        code
                    ")
                    ->get();
                
                // Group subjects for display
                $availableSubjects = $allSubjects->groupBy(['year_level', 'semester']);
            }
        }

        // Get student's enrolled subjects for the current school year
        $currentYearEnrolled = $enrolledSubjects->filter(function($enrolled) use ($student) {
            // You might need to filter by school year if you store it
            return true; // For now, return all
        });

        // Calculate current year stats
        $currentYearSubjects = $currentYearEnrolled->count();
        $currentYearUnits = 0;
        $currentYearGradePoints = 0;
        
        foreach ($currentYearEnrolled as $enrolled) {
            if ($enrolled->subject) {
                $units = $enrolled->subject->units ?? 3;
                $currentYearUnits += $units;
                
                $numericGrade = $this->convertGradeToNumeric($enrolled->grade);
                $currentYearGradePoints += ($numericGrade * $units);
            }
        }

        $currentYearGWA = $currentYearUnits > 0 ? $currentYearGradePoints / $currentYearUnits : 0;
    
        // Get grade distribution
        $gradeDistribution = [
            'excellent' => 0, // 1.0-1.5
            'good' => 0,      // 1.6-2.0
            'fair' => 0,      // 2.1-2.5
            'passing' => 0,   // 2.6-3.0
            'failing' => 0,   // 4.0-5.0
            'special' => 0,   // INC, DRP
        ];

        foreach ($enrolledSubjects as $enrolled) {
            $numericGrade = $this->convertGradeToNumeric($enrolled->grade);
            
            if ($numericGrade >= 1.0 && $numericGrade <= 1.5) {
                $gradeDistribution['excellent']++;
            } elseif ($numericGrade >= 1.6 && $numericGrade <= 2.0) {
                $gradeDistribution['good']++;
            } elseif ($numericGrade >= 2.1 && $numericGrade <= 2.5) {
                $gradeDistribution['fair']++;
            } elseif ($numericGrade >= 2.6 && $numericGrade <= 3.0) {
                $gradeDistribution['passing']++;
            } elseif ($numericGrade >= 4.0 && $numericGrade <= 5.0) {
                $gradeDistribution['failing']++;
            } elseif ($enrolled->grade === 'INC' || $enrolled->grade === 'DRP') {
                $gradeDistribution['special']++;
            }
        }

        // Get recent enrolled subjects (last 5)
        $recentSubjects = $enrolledSubjects->take(5);

        $academicProgress = $enrolledSubjects->map(function($enrolled) {
            $subject = $enrolled->subject;
            $numericGrade = $this->convertGradeToNumeric($enrolled->grade);
            
            // Calculate progress percentage
            $progressPercentage = 0;
            if ($numericGrade >= 1.0 && $numericGrade <= 5.0) {
                // For grades 1.0-5.0: 1.0 = 100%, 5.0 = 0%
                $progressPercentage = 100 - (($numericGrade - 1.0) / 4.0 * 100);
            } elseif ($enrolled->grade === 'PASS') {
                $progressPercentage = 100;
            } elseif ($enrolled->grade === 'FAIL') {
                $progressPercentage = 0;
            }
            
            return [
                'subject_name' => $subject->name ?? 'Unknown Subject',
                'subject_code' => $subject->code ?? 'N/A',
                'grade' => $enrolled->grade,
                'numeric_grade' => $numericGrade,
                'progress_percentage' => $progressPercentage,
                'color' => $this->getGradeColor($numericGrade),
                'units' => $subject->units ?? 3,
                'status' => $this->getGradeStatus($numericGrade)
            ];
        })->sortByDesc('numeric_grade')->take(4);

        $gradesTableData = $enrolledSubjects->map(function($enrolled) {
            $subject = $enrolled->subject;
            $numericGrade = $this->convertGradeToNumeric($enrolled->grade);
            
            // Get equivalent letter grade or status
            $equivalent = $this->getGradeEquivalent($enrolled->grade);
            
            return [
                'subject_code' => $subject->code ?? 'N/A',
                'subject_name' => $subject->name ?? 'Unknown Subject',
                'grade' => $enrolled->grade,
                'numeric_grade' => $numericGrade,
                'equivalent' => $equivalent,
                'units' => $subject->units ?? 3,
                'color_class' => $this->getGradeColorClass($numericGrade),
            ];
        })->sortBy('subject_code');

        $academicStanding = $this->getAcademicStanding($gwa);

        return view('student.dashboard', compact(
            'user', 
            'pageTitle',
            'sem',
            'hasEnrolledSubjects',
            'availableSubjects',
            'studentID',
            'student',
            // 'address',
            'hasEnrolledSubjects',
        
            // Real data for dashboard
            'gwa',
            'currentYearGWA',
            'totalSubjects',
            'totalUnits',
            'currentYearSubjects',
            'currentYearUnits',
            'enrolledSubjects',
            'recentSubjects',
            'gradeDistribution',
            'academicProgress',
            'gradesTableData',
            'academicStanding'
        ));

    }

    // Add to StudentController.php
    private function getGradeStatus($numericGrade)
    {
        if ($numericGrade >= 1.0 && $numericGrade <= 1.5) {
            return 'Excellent';
        } elseif ($numericGrade >= 1.6 && $numericGrade <= 2.0) {
            return 'Very Good';
        } elseif ($numericGrade >= 2.1 && $numericGrade <= 2.5) {
            return 'Good';
        } elseif ($numericGrade >= 2.6 && $numericGrade <= 3.0) {
            return 'Satisfactory';
        } elseif ($numericGrade >= 4.0 && $numericGrade <= 5.0) {
            return 'Needs Improvement';
        } else {
            return 'Pending';
        }
    }


    // Add this method to StudentController.php
    private function getGradeColor($numericGrade)
    {
        if ($numericGrade >= 1.0 && $numericGrade <= 1.5) {
            return '#28a745'; // Green - Excellent
        } elseif ($numericGrade >= 1.6 && $numericGrade <= 2.0) {
            return '#17a2b8'; // Cyan - Very Good
        } elseif ($numericGrade >= 2.1 && $numericGrade <= 2.5) {
            return '#ffc107'; // Yellow - Good
        } elseif ($numericGrade >= 2.6 && $numericGrade <= 3.0) {
            return '#fd7e14'; // Orange - Satisfactory
        } elseif ($numericGrade >= 4.0 && $numericGrade <= 5.0) {
            return '#dc3545'; // Red - Failing
        } else {
            return '#6c757d'; // Gray - Special grades
        }
    }


    // Add a new method to handle subject enrollment submission
    public function enrollSubjects(Request $request)
    {
        try {
            $user = Auth::guard('student')->user();
            $student = $user->user_information->student;
            $studentID = $student->id;
            // $studentId = $request->input('student_id');
            $enrolledSubjects = $request->input('enrolled_subjects');
            
            // Validate student exists
            $student = Student::where('id', $studentID)->first();
            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student not found'
                ]);
            }
            
            // Validate input
            if (empty($enrolledSubjects) || !is_array($enrolledSubjects)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No subjects selected'
                ]);
            }
            
            $enrollments = [];
            $totalGradePoints = 0;
            $totalUnits = 0;
            
            // Process each subject
            foreach ($enrolledSubjects as $subject) {
                if (!isset($subject['subject_id'], $subject['grade'])) {
                    continue;
                }
                
                // Get subject details for units
                $subjectDetail = Subject::find($subject['subject_id']);
                if (!$subjectDetail) {
                    continue;
                }
                
                // Convert grade to numeric for GWA calculation
                $numericGrade = $this->convertGradeToNumeric($subject['grade']);
                
                // Save to enrolled_subjects table
                EnrolledSubjects::create([
                    'student_id' => $studentID,
                    'subject_id' => $subject['subject_id'],
                    'grade' => $subject['grade']
                ]);
                
                // Calculate for GWA
                $units = $subjectDetail->units ?: 3; // Default to 3 units if not specified
                $totalGradePoints += ($numericGrade * $units);
                $totalUnits += $units;
                
                $enrollments[] = [
                    'subject_id' => $subject['subject_id'],
                    'grade' => $subject['grade'],
                    'units' => $units
                ];
            }
            
            // Calculate GWA
            $gwa = $totalUnits > 0 ? $totalGradePoints / $totalUnits : 0;
            
            // Update student status to officially enrolled
            $student->status = 'Officially Enrolled';
            $student->enrolled = 1;
            $student->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Subjects enrolled successfully',
                'gwa' => number_format($gwa, 2),
                'enrollment_count' => count($enrollments)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    // Helper method to convert letter grades to numeric
    private function convertGradeToNumeric($grade)
    {
        $gradeMap = [
            // Passing Grades
            '1.0' => 1.0, '1.1' => 1.1, '1.2' => 1.2, '1.3' => 1.3, '1.4' => 1.4,
            '1.5' => 1.5, '1.6' => 1.6, '1.7' => 1.7, '1.8' => 1.8, '1.9' => 1.9,
            '2.0' => 2.0, '2.1' => 2.1, '2.2' => 2.2, '2.3' => 2.3, '2.4' => 2.4,
            '2.5' => 2.5, '2.6' => 2.6, '2.7' => 2.7, '2.8' => 2.8, '2.9' => 2.9,
            '3.0' => 3.0,

            // Failing and Special Grades
            '4.0' => 4.0, '5.0' => 5.0,
            'INC' => 0.0, 'DRP' => 0.0,
            
            // Original descriptive mappings
            'PASS' => 1.0, 'FAIL' => 5.0,
        ];
        
        return $gradeMap[$grade] ?? 0;
    }

    // helper method to get grade equivalent
    private function getGradeEquivalent($grade)
    {
        $gradeMap = [
            // Passing grades
            '1.0' => '1.0', '1.1' => '1.1', '1.2' => '1.2', '1.3' => '1.3', '1.4' => '1.4',
            '1.5' => '1.5', '1.6' => '1.6', '1.7' => '1.7', '1.8' => '1.8', '1.9' => '1.9',
            '2.0' => '2.0', '2.1' => '2.1', '2.2' => '2.2', '2.3' => '2.3', '2.4' => '2.4',
            '2.5' => '2.5', '2.6' => '2.6', '2.7' => '2.7', '2.8' => '2.8', '2.9' => '2.9',
            '3.0' => '3.0',
            
            // Failing and special grades
            '4.0' => '4.0',
            '5.0' => '5.0',
            'INC' => 'INC',
            'DRP' => 'DRP',
            'PASS' => 'PASS',
            'FAIL' => 'FAIL',
        ];
        
        return $gradeMap[$grade] ?? $grade;
    }

    // helper method to get grade color class for badges
    private function getGradeColorClass($numericGrade)
    {
    if ($numericGrade >= 1.0 && $numericGrade <= 1.5) {
        return 'grade-excellent'; // Green
    } elseif ($numericGrade >= 1.6 && $numericGrade <= 2.5) {
        return 'grade-good'; // Blue
    } elseif ($numericGrade >= 2.6 && $numericGrade <= 3.0) {
        return 'grade-passing'; // Yellow
    } elseif ($numericGrade >= 4.0 && $numericGrade <= 5.0) {
        return 'grade-failing'; // Red
    } else {
        return 'grade-special'; // Gray
    }
}

    // Determine academic standing
    private function getAcademicStanding($gwa)
    {
        if ($gwa >= 1.0 && $gwa <= 1.45) {
            return 'President\'s Lister';
        } elseif ($gwa >= 1.46 && $gwa <= 1.75) {
            return 'Dean\'s Lister';
        } elseif ($gwa >= 1.76 && $gwa <= 2.00) {
            return 'Academic Achiever';
        } elseif ($gwa >= 2.01 && $gwa <= 2.75) {
            return 'Satisfactory';
        } else {
            return 'On Probation';
        }
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