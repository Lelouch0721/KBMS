<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Borrow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;



class BorrowsController extends Controller
{
    public function index()
    {
        $borrows = borrow::leftJoin('items', 'borrows.item_id', '=', 'items.id')
            ->leftJoin('users', 'borrows.borrower', '=', 'users.email')
            ->select(
                'borrows.*',
                'users.name as borrower',
                'items.name as key',
                DB::raw('CONCAT(DATE_FORMAT(borrows.created_at, "%M %d, %Y %h:%i %p")) as date_borrowed')
            )
            ->whereIn('status', ['borrowed'])
            ->get();
            
        return view('key.borrows', ['borrows' => $borrows]);
    }

    public function update(Request $request)
    {
        $decrypted = Crypt::decryptString($request->id);
    
        // Attempt to connect to the Arduino to check availability
        try {
            // Fetch the borrow record associated with the decrypted ID
            $borrow = Borrow::find($decrypted);
            if (!$borrow) {
                session()->flash('message', 'Borrow record not found!');
                session()->flash('color', 'error');
                return redirect(route('borrows.view'));
            }
    
            $itemId = $borrow->item_id;
    
            $item = \App\Models\Item::find($itemId);
            if (!$item) {
                session()->flash('message', 'Item not found!');
                session()->flash('color', 'error');
                return redirect(route('borrows.view'));
            }
            $itemType = $item->type;
            $arduinoResponse = Http::timeout(5)->get("http://192.168.1.15/unlock?id={$itemId}");
    
            if ($arduinoResponse->successful()) {
                $status = ($itemType == 'remote') ? 'returned (remote)' : 'returned (key)';
                $update = Borrow::where('id', $decrypted)
                    ->where('status', 'borrowed')
                    ->update([
                        'status' => $status,
                    ]);
    
                if ($update) {
                    session()->flash('message', ucfirst($itemType) . ' returned successfully!');
                    session()->flash('color', 'success');
                    return redirect(route('borrows.view'));
                }
    
                session()->flash('message', 'Error updating status!');
                session()->flash('color', 'error');
                return redirect(route('borrows.view'));
            } else {
                session()->flash('message', 'Status update aborted.');
                session()->flash('color', 'error');
                return redirect(route('borrows.view'));
            }
    
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            session()->flash('message', 'Failed to connect to return. Please try again!');
            session()->flash('color', 'error');
            return redirect(route('borrows.view'));
        } catch (\Exception $e) {
            session()->flash('message', 'An unexpected error occurred: ' . $e->getMessage());
            session()->flash('color', 'error');
            return redirect(route('borrows.view'));
        }
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
    
        return view('key.history', ['borrows' => $borrows]);
    }

    public function deleteByMonth(Request $request)
    {
        $month = $request->input('month');
    
        if (!$month) {
            return response()->json(['success' => false, 'message' => 'No month specified.'], 400);
        }
    
        $deleted = DB::table('borrows')
            ->where('status', 'returned')
            ->whereRaw("DATE_FORMAT(date_served, '%Y-%m') = ?", [$month])
            ->delete();
    
        if ($deleted) {
            return response()->json(['success' => true, 'message' => "Records for $month have been deleted."]);
        }
    
        return response()->json(['success' => false, 'message' => 'No records found for the selected month.'], 404);
    }
    

}
