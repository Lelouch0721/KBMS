<?php

namespace App\Http\Controllers\UserPages;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use App\Models\Item;
use Illuminate\Support\Facades\DB;
use App\Models\Borrow;
use Illuminate\Support\Facades\Http;

class UserController extends Controller
{
    public function landingGuest(Request $request)
    {
        $searchTerm = $request->input('search', '');
    
        $products = Item::where('name', 'like', "%{$searchTerm}%")->orderBy('name')->get();
    
        $recentBorrowedKey = $this->getRecentBorrowedKey();
    
        $borrowedKeys = Borrow::where('status', 'borrowed')
            ->leftJoin('users', 'borrows.borrower', '=', 'users.email')
            ->select('borrows.item_id', 'users.name as borrower_name')
            ->get();
    
        return view('user_pages.dashboard_user', [
            'products' => $products,
            'recentBorrowedKey' => $recentBorrowedKey,
            'borrowedKeys' => $borrowedKeys,
            'searchTerm' => $searchTerm
        ]);
    }
    
    // public function borrow(Request $request)
    // {
    //     try {
    //         $decrypted = Crypt::decryptString($request->id);
    //         $getItemInfo = Item::findOrFail($decrypted);
    //         $producttype = strtolower($getItemInfo->type);
    //         $existingBorrows = Borrow::where('borrower', Auth::user()->email)
    //             ->where('status', 'borrowed')
    //             ->get();
    
    //         $hasBorrowedKey = false;
    //         $hasBorrowedRemote = false;
    
    //         foreach ($existingBorrows as $borrow) {
    //             $borrowedItem = Item::findOrFail($borrow->item_id);
    //             $borrowedtype = strtolower($borrowedItem->type);
    
    //             if ($borrowedtype === 'key') {
    //                 $hasBorrowedKey = true;
    //             } elseif ($borrowedtype === 'remote') {
    //                 $hasBorrowedRemote = true;
    //             }
    //         }
    
    //         if ($producttype === 'key' && $hasBorrowedKey) {
    //             session()->flash('message', 'You can only borrow one key at a time!');
    //             session()->flash('color', 'error');
    //             return redirect()->route('guest');
    //         }
    
    //         if ($producttype === 'remote' && $hasBorrowedRemote) {
    //             session()->flash('message', 'You can only borrow one remote at a time!');
    //             session()->flash('color', 'error');
    //             return redirect()->route('guest');
    //         }
    
    //         $itemId = $getItemInfo->id;
    //         $arduinoIPs = ['http://192.168.1.12/unlock', 'http://192.168.1.16/unlock'];
    
    //         foreach ($arduinoIPs as $ip) {
    //             $response = Http::get("{$ip}?id={$itemId}");
    //             if ($response->successful()) {
    //                 Borrow::create([
    //                     'borrower' => Auth::user()->email,
    //                     'type' => $getItemInfo->type,
    //                     'item_id' => $itemId,
    //                     'status' => 'borrowed',
    //                 ]);
    
    //                 session()->flash('message', 'Borrow Successful!');
    //                 session()->flash('color', 'success');
    //                 return redirect()->route('guest');
    //             }
    //         }
    
    //         session()->flash('message', 'Error: Unable to connect to both Arduinos!');
    //         session()->flash('color', 'error');
    //         return redirect()->route('guest');
    //     } catch (\Exception $e) {
    //         session()->flash('message', 'Error Borrowing item!');
    //         session()->flash('color', 'error');
    //         return redirect()->route('guest');
    //     }
    // }
    
    // public function return(Request $request)
    // {
    //     try {
    //         $decrypted = Crypt::decryptString($request->id);
    
    //         $borrow = Borrow::where('item_id', $decrypted)
    //             ->where('borrower', Auth::user()->email)
    //             ->where('status', 'borrowed')
    //             ->first();
    
    //         if ($borrow) {
    //             $itemId = $borrow->item_id;
    //             $arduinoIPs = ['http://192.168.1.15/unlock', 'http://192.168.1.16/unlock'];
    
    //             foreach ($arduinoIPs as $ip) {
    //                 $response = Http::get("{$ip}?id={$itemId}");
    //                 if ($response->successful()) {
    //                     $borrow->update(['status' => 'returned']);
    //                     session()->flash('message', 'Key Returned Successfully!');
    //                     session()->flash('color', 'success');
    //                     return redirect()->route('guest');
    //                 }
    //             }
    
    //             session()->flash('message', 'Error: Unable to connect to both Arduinos!');
    //             session()->flash('color', 'error');
    //         } else {
    //             session()->flash('message', 'You did not borrow this item!');
    //             session()->flash('color', 'error');
    //         }
    
    //         return redirect()->route('guest');
    //     } catch (\Exception $e) {
    //         session()->flash('message', 'Error Returning Key!');
    //         session()->flash('color', 'error');
    //         return redirect()->route('guest');
    //     }
    // }    
     
    public function deleteAllReturned()
    {
        Borrow::where('status', 'returned')->delete();
    
        session()->flash('message', 'All returned keys and remotes have been cleared!');
        session()->flash('color', 'success');
        return redirect()->route('history.view');
    }
    
    public function search(Request $request)
    {
        $searchTerm = trim($request->input('search', ''));

        $products = Item::where('name', $searchTerm)->orderBy('name')->get();

        $borrowedKeys = Borrow::where('borrower', Auth::user()->email)
            ->where('status', 'borrowed')
            ->pluck('item_id');

        $recentBorrowedKey = $this->getRecentBorrowedKey();

        return view('user_pages.dashboard_user', [
            'products' => $products,
            'recentBorrowedKey' => $recentBorrowedKey,
            'borrowedKeys' => $borrowedKeys,
            'searchTerm' => $searchTerm
        ]);
    }

    private function getRecentBorrowedKey()
    {
        if (!Auth::check()) {
            return null; 
        }
    
        return Borrow::leftJoin('items', 'borrows.item_id', '=', 'items.id')
            ->select('borrows.*', 'items.name as key',
                DB::raw('DATE_FORMAT(borrows.created_at, "%M %d, %Y %h:%i %p") as date_borrowed'))
            ->where('borrows.borrower', Auth::user()->email)
            ->where('borrows.status', 'borrowed')
            ->orderBy('borrows.created_at', 'desc')
            ->first();
    }
    
    public function recentBorrowedKeys()
    {
        $recentBorrowedKeysForAll = Borrow::leftJoin('items', 'borrows.item_id', '=', 'items.id')
            ->leftJoin('users', 'borrows.borrower', '=', 'users.email')
            ->select(
                'borrows.id',
                'users.name as borrower_name',
                'items.name as key_name',
                DB::raw('DATE_FORMAT(borrows.created_at, "%M %d, %Y %h:%i %p") as date_borrowed')
            )
            ->where('borrows.status', 'borrowed')
            ->where('items.type', 'key') 
            ->orderBy('borrows.created_at', 'desc')
            ->get();
    
        return view('user_pages.recent_borrowed_keys', [
            'recentBorrowedKeysForAll' => $recentBorrowedKeysForAll
        ]);
    }    

    public function recentBorrowedRemotes()
    {
        $recentBorrowedRemotesForAll = DB::table('borrows')
        ->leftJoin('items', 'borrows.item_id', '=', 'items.id')
        ->leftJoin('users', 'borrows.borrower', '=', 'users.email')
        ->select('borrows.id', 'users.name as borrower_name', 'items.name as remote_name', DB::raw('DATE_FORMAT(borrows.created_at, "%M %d, %Y %h:%i %p") as date_borrowed'))
        ->where('borrows.status', 'borrowed')
        ->where('items.type', 'remote') 
        ->orderBy('borrows.created_at', 'desc')
        ->get();
    
        return view('user_pages.recent_borrowed_remote', compact('recentBorrowedRemotesForAll'));
    }

        
    public function history()
    {
        $borrows = Borrow::leftJoin('items', 'borrows.item_id', '=', 'items.id')
            ->leftJoin('users', 'borrows.borrower', '=', 'users.email')
            ->select(
                'borrows.*',
                'users.name as borrower',
                'items.name as key',
                DB::raw('MONTH(borrows.updated_at) as month'),  
                DB::raw('WEEKDAY(borrows.updated_at) as day_of_week'),  
                DB::raw('DATE_FORMAT(borrows.updated_at, "%M %d, %Y %h:%i %p") as date_served')
            )
            ->where('status', 'returned')
            ->get();
    
        return view('user_pages.user_returned', ['borrows' => $borrows]);
    }
}
