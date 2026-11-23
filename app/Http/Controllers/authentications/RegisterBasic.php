<?php

namespace App\Http\Controllers\authentications;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RegisterBasic extends Controller
{
    public function index()
    {
        return view('content.authentications.auth-register-basic');
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|unique:users|max:255',
            'email' => 'required|string|email|unique:users,email',
            'password' => [
                'required',
                'string',
                'min:8',            
                'regex:/[A-Z]/',    
                'regex:/[0-9]/',    
            ],
        ];
    
        $messages = [
            'name.required' => 'Username is required.',
            'name.unique' => 'This username is already taken',
            'email.required' => 'Email is required.',
            'email.email' => 'Email must be a valid email address.',
            'email.unique' => 'This email is already taken.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters long.',
            'password.regex' => 'Password must contain at least one uppercase letter and one number.',
        ];
    
        $validatedData = $request->validate($rules, $messages);
    
        $user = new User();
        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];
        $user->password = bcrypt($validatedData['password']); 
        $user->user_role = "User"; 
        $user->qr_token = Str::random(32); 
        $user->save(); 
    
        $admins = User::where('user_role', 'Admin')->get();
        foreach ($admins as $admin) {
            \Mail::raw("A new user named {$user->name} has registered and is awaiting approval.", function ($message) use ($admin) {
                $message->to($admin->email)
                        ->subject('New User Registration Approval');
            });
        }
    
        session()->flash('message', 'Registered Successfully! Awaiting admin approval.');
        session()->flash('color', 'success');
    
        return redirect()->route('auth-login-basic');
    }    
}
