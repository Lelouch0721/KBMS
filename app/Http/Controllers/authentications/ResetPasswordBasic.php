<?php

namespace App\Http\Controllers\authentications;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ResetPasswordBasic extends Controller
{
    public function showResetForm(Request $request, $token)
    {
        return view('content.authentications.auth-reset-password-basic', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }
}
