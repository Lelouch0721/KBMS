<?php

namespace App\Http\Controllers\authentications;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;

class ForgotPasswordBasic extends Controller
{
    public function index()
    {
        return view('content.authentications.auth-forgot-password-basic');
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required|sometimes',
            'email' => 'required|email',
            'password' => [
                'required',
                'string',
                'min:8', // Minimum of 8 characters
                'confirmed', // Must match password_confirmation
                'regex:/[A-Z]/', // At least one uppercase letter
            ],
        ], [
            'token.required' => 'Invalid token. Please try again.',
        ]);
    
        if (!$request->token) {
            return redirect()->route('auth-login-basic')->with('error', 'Invalid token. Please try again.');
        }
    
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );
    
        return $status === Password::PASSWORD_RESET
            ? redirect()->route('auth-login-basic')->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        
        $status = Password::sendResetLink($request->only('email'));
        
        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    // Show the form to reset the password
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.passwords.reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }
}
