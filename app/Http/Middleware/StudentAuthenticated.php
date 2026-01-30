<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('student')->check()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
            return redirect('/');
        }
        
        return $next($request);
    }
}