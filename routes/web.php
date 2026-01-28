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


// Public routes
Route::get('/', function () {
    return view('welcome');
});

// Student Routes
Route::prefix('/exe')->group(function (){
    Route::post('/student', [StudentController::class, 'register']);
});