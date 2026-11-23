<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Item;
use App\Models\Borrow;
use Carbon\Carbon;

class Analytics extends Controller
{
    public function index()
    {
        // Count the number of admins
        $admin = User::where('user_role', 'Admin')->count();
        
        // Count the total number of users
        $user = User::count();
        
        // Count the total number of items (adjust if needed)
        $item = Item::count();
        
        // Count the number of borrows with status 'borrowed' (i.e., borrowed keys)
        $borrowedKeys = Borrow::where('status', 'borrowed')->count();
        
        // Count the number of borrows with status 'returned'
        $returnedKeys = Borrow::where('status', 'returned')->count();

            // Determine if there are overdue items (e.g., borrowed items past their due time)
    $overdueItemsExist = Borrow::where('status', 'borrowed')
    ->whereTime('created_at', '<', Carbon::now()->subHours(12)) // Example logic for overdue
    ->exists();

        // Prepare data to be sent to the view
        $data = [
            'admin' => $admin,
            'user' => $user,
            'item' => $item,
            'borrowedKeys' => $borrowedKeys,
            'returnedKeys' => $returnedKeys, // Add returned keys count to the data
            'overdueItemsExist' => $overdueItemsExist,
        ];

        // Pass data to the view and render it
        return view('content.dashboard.dashboards-analytics', ['data' => $data]);
    }
}
