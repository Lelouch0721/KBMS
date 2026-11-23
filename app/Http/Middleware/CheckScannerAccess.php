<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckScannerAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Redirect admins to their dashboard
        if (Auth::check() && Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        // Redirect regular users to their dashboard if not allowed to scan
        if (Auth::check() && !Auth::user()->canAccessScanner()) {
            return redirect()->route('user.dashboard');
        }

        // Allow access to the scanner page
        return $next($request);
    }
}
