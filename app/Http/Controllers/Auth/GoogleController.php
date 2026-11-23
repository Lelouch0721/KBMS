<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;

class GoogleController extends Controller
{
    public function GoogleAuth(Request $request)
    {
        $throttleKey = $this->throttleKey();

        // Check for too many attempts
        if (RateLimiter::tooManyAttempts($throttleKey, 3, 60)) {
            return response()->json('limit');
        }

        // Find the user by email
        $user = User::where('email', $request['email'])->first();

        // If the user is not found, increment throttle attempts and return 'unauthorized'
        if (!$user) {
            RateLimiter::hit($throttleKey);
            return response()->json('unauthorized');
        }

        // Check if the user is approved
        if (!$user->is_approved) {
            RateLimiter::hit($throttleKey); // Increment throttle attempts
            return response()->json(['message' => 'not_approved']);
        }

        // Log in the user if all checks pass
        Auth::login($user);
        $role = $user->user_role;

        // Clear throttle attempts
        RateLimiter::clear($throttleKey);

        return response()->json(['role' => $role, 'message' => 'success']);
    }

    private function throttleKey()
    {
        // Generate a unique throttle key based on the user's IP address
        return 'google_auth_' . request()->ip();
    }
}
