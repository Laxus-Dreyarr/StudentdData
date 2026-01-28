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

                Cache::put('registration_' . $request->email, [
                    'otp' => $verificationCode,
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
                    'zip_code' => $request->zip_code,
                    'relationship_status' => $request->status,
                    'is_regular' => '2',
                    'year_level' => "NONE",
                    'curriculum' => $assignedCurriculum,
                    'attempts' => 0,
                ], now()->addMinutes(10));

                session(['registration_email' => $request->email]);

                // Send verification email
                try {
                    Mail::to($request->email)->send(new RegistrationVerification($verificationCode, $request->givenName, $request->lastName));

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

                // Check password confirmation
                if ($request->password !== $request->repeatPassword) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Passwords do not match.'
                    ]);
                }

                // Generate verification code (6 digits)
                $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

                // Get device information
                $deviceInfo = $this->getDeviceInfo();

                Cache::put('registration_' . $request->email, [
                    'otp' => $verificationCode,
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
                    'zip_code' => $request->zip_code,
                    'relationship_status' => $request->status,
                    'is_regular' => '1',
                    'year_level' => "1st Year",
                    'curriculum' => $assignedCurriculum,
                    'attempts' => 0,
                    'ip_address' => request()->ip(),
                    'device_info' => $deviceInfo,
                    'device_summary' => $this->getDeviceSummary()
                ], now()->addMinutes(10));

                session(['registration_email' => $request->email]);

                // Send verification email
                try {
                    Mail::to($request->email)->send(new RegistrationVerification($verificationCode, $request->givenName, $request->lastName));

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

}
