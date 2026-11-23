<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Pizza;  // Correct import
use App\Models\User;  // Correct import
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;


class AccountController extends Controller
{
    public function index()
    {
        $users = User::all(); // Retrieve all keys from the database
        return view('key.users', ['users' => $users]);
    }
    
    public function delete(Request $request)
    {
        $decrypted = Crypt::decryptString($request->id);

        $delete = User::where('id', $decrypted)->delete();

        if ($delete) {
            session()->flash('message', 'The account has been rejected and deleted!');
            session()->flash('color', 'success');
            return redirect (route('user.view'));
        }
        session()->flash('message', 'Error deleting Account!');
        session()->flash('color', 'error');
        return redirect (route('user.view'));
    }

    public function approve(Request $request)
    
    {
        $userId = Crypt::decryptString($request->id);
        $user = User::findOrFail($userId);
        $user->is_approved = true;
        $user->save();

        return redirect()->back()->with(['message' => 'User approved successfully!', 'color' => 'success']);
    }

    public function dashboard()
    {
        // Count users who are not yet approved
        $pendingApprovals = User::where('is_approved', false)->count();

        // Pass the count to the dashboard view
        return view('content.authentications.dashboard', compact('pendingApprovals'));
    }

}
