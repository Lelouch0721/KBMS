<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\User;
use DB;
use Illuminate\Support\Facades\Http;
use RealRashid\SweetAlert\Facades\SweetAlert;

class QrController extends Controller
{
    public function showQrCode()
    {
        $user = auth()->user();

        if (!$user->qr_token) {
            $user->qr_token = \Str::random(32);  
            $user->save();
        }

        $urlKey = route('qr.return', ['token' => $user->qr_token, 'type' => 'key']);
        $urlRemote = route('qr.return', ['token' => $user->qr_token, 'type' => 'remote']);
        $urlBoth = route('qr.return', ['token' => $user->qr_token, 'type' => 'both']);

        $qrCodeKey = QrCode::size(250)->generate($urlKey);
        $qrCodeRemote = QrCode::size(250)->generate($urlRemote);
        $qrCodeBoth = QrCode::size(250)->generate($urlBoth);

        return view('qr.qr', compact('qrCodeKey', 'qrCodeRemote', 'qrCodeBoth', 'user'));
    }

    public function handleQrReturn($token)
    {
        $type = request()->query('type');
        
        $user = User::where('qr_token', $token)->first();
        
        if (!$user) {
            return redirect()->route('auth-login-basic')->with('error', 'The QR code is not valid or has expired.');
        }
    
        if ($type === 'both') {
            $borrowedItems = DB::table('borrows')
                ->where('borrower', $user->email)
                ->where('status', 'borrowed')
                ->whereIn('type', ['key', 'remote'])
                ->get();
    
            if ($borrowedItems->isEmpty()) {
                return redirect()->route('auth-login-basic')->with('error', 'No borrowed items found.');
            }
    
            $allSuccess = true;
    
            foreach ($borrowedItems as $item) {
                $itemId = $item->item_id;
                try {
                    $response = Http::get("http://192.168.1.15/unlock?id={$itemId}");
    
                    if ($response->successful()) {
                        DB::table('borrows')
                            ->where('id', $item->id)
                            ->update(['status' => 'returned', 'updated_at' => now()]);
                    } else {
                        $allSuccess = false;
                    }
                } catch (\Illuminate\Http\Client\ConnectionException $e) {
                    return redirect()->route('auth-login-basic')->with('error', 'Unable to connect to the device. Please check your network connection.');
                } catch (\Exception $e) {
                    return redirect()->route('auth-login-basic')->with('error', 'An error occurred while returning the item.');
                }
            }
    
            if ($allSuccess) {
                return redirect()->route('auth-login-basic')->with('success', 'Both items returned successfully.');
            } else {
                return redirect()->route('auth-login-basic')->with('error', 'Error unlocking one or more items. Please try again.');
            }
        } else {
            $borrowedItem = DB::table('borrows')
                ->where('borrower', $user->email)
                ->where('status', 'borrowed')
                ->where('type', $type)
                ->first();
            
            if (!$borrowedItem) {
                return redirect()->route('auth-login-basic')->with('error', 'No borrowed item found.');
            }
    
            $itemId = $borrowedItem->item_id;
    
            try {
                $response = Http::get("http://192.168.1.15/unlock?id={$itemId}");
    
                if ($response->successful()) {
                    DB::table('borrows')
                        ->where('id', $borrowedItem->id)
                        ->update(['status' => 'returned', 'updated_at' => now()]);
                    
                    return redirect()->route('auth-login-basic')->with('success', 'Item returned successfully and unlocked.');
                } else {
                    return redirect()->route('auth-login-basic')->with('error', 'Error unlocking the item. Please try again.');
                }
            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                return redirect()->route('auth-login-basic')->with('error', 'Unable to connect to the device. Please check your network connection.');
            } catch (\Exception $e) {
                return redirect()->route('auth-login-basic')->with('error', 'An error occurred while returning the item.');
            }
        }
    }
}    