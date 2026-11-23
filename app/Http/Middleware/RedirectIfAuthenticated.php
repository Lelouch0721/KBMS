<?php 

namespace App\Http\Middleware;  

use Closure; 
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Auth; 
use Symfony\Component\HttpFoundation\Response;  

class RedirectIfAuthenticated 
{ 
    /** 
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string|null  ...$guards
     * @return \Symfony\Component\HttpFoundation\Response
     */ 
    public function handle(Request $request, Closure $next, ...$guards): Response 
    { 
        // Check if the user is authenticated 
        if (Auth::check()) { 
            $user = Auth::user(); 

            // If the user is an admin and tries to access the login page
            if ($user->isAdmin() && $request->routeIs('auth-login-basic')) { 
                return redirect()->route('dashboard-analytics'); // Redirect to admin dashboard
            } 

            // If the user is not an admin and tries to access the login page
            if (!$user->isAdmin() && $request->routeIs('auth-login-basic')) { 
                return redirect()->route('guest'); // Redirect to user dashboard 
            } 
        } 

        return $next($request); 
    } 
}
