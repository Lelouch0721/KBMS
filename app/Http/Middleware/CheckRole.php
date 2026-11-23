<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Check if the user is an admin
            if ($user->isAdmin()) {
                // Admins should not be redirected to the login page
                // Implement prevention for caching and back button behavior
                return $next($request);
            } else {
                // If the user role is "User"
                if ($user->role === 'User') {
                    // Redirect non-admin users to the guest page
                    return redirect()->route('guest');
                }
                // For other roles or unknown roles
                return redirect()->route('guest');
            }
        } else {
            // Redirect unauthenticated users to the login page
            return redirect()->route('auth-login-basic');
        }
    }
}
