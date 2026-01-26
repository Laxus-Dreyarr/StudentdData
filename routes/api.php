<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GradeApiController;
use App\Http\Controllers\Api\EnrollmentApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Public API routes (no API key required for these)

// Protected API routes (require API key)

// Debug route - REMOVE THIS IN PRODUCTION
// Route::get('/debug-routes', function() {
//     $routes = collect(\Illuminate\Support\Facades\Route::getRoutes()->getRoutes())
//         ->map(function ($route) {
//             return [
//                 'method' => implode('|', $route->methods()),
//                 'uri' => $route->uri(),
//                 'name' => $route->getName(),
//                 'action' => $route->getActionName(),
//             ];
//         })
//         ->filter(function ($route) {
//             return strpos($route['uri'], 'api/') === 0 || strpos($route['uri'], 'student') !== false;
//         })
//         ->values();
    
//     return response()->json($routes);
// });