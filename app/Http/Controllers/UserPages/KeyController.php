<?php

namespace App\Http\Controllers\UserPages;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Borrow;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class KeyController extends Controller
{

    public function borrow(Request $request)
    {
        try {
            $decrypted = Crypt::decryptString($request->id);
            Log::info('Decrypted Item ID:', ['id' => $decrypted]);

            $getItemInfo = Item::find($decrypted);
            if (!$getItemInfo) {
                return response()->json(['title' => 'Error', 'message' => 'Item not found!'], 404);
            }

            $producttype = strtolower($getItemInfo->type);
            $existingBorrows = Borrow::where('borrower', Auth::user()->email)
                ->where('status', 'borrowed')
                ->get();

            Log::info('Existing borrows:', ['borrows' => $existingBorrows]);

            foreach ($existingBorrows as $borrow) {
                $borrowedItem = Item::find($borrow->item_id);
                if ($borrowedItem && strtolower($borrowedItem->type) === $producttype) {
                    return response()->json(['title' => 'Error', 'message' => 'You can only borrow one ' . $producttype . ' at a time!'], 400);
                }
            }

            $itemId = $getItemInfo->id;
            $arduinoIPs = ['http://192.168.1.222/unlock', 'http://192.168.1.16/unlock'];

            foreach ($arduinoIPs as $ip) {
                Log::info('Arduino Request:', ['url' => "{$ip}?id={$itemId}"]);
                $response = Http::get("{$ip}?id={$itemId}");

                if ($response->successful()) {
                    Borrow::create([
                        'borrower' => Auth::user()->email,
                        'type' => $getItemInfo->type,
                        'item_id' => $itemId,
                        'status' => 'borrowed',
                    ]);

                    $this->database->getReference('borrows/' . $itemId)->set([
                        'borrower' => Auth::user()->email,
                        'type' => $getItemInfo->type,
                        'status' => 'borrowed',
                        'timestamp' => now()->toDateTimeString(),
                    ]);

                    return response()->json(['title' => 'Success', 'message' => ucfirst($producttype) . ' borrowed successfully!'], 200);
                }
            }

            return response()->json(['title' => 'Error', 'message' => 'Unable to connect to both Arduinos!'], 500);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return response()->json(['title' => 'Error', 'message' => 'Invalid ID format!'], 400);
        } catch (\Exception $e) {
            Log::error('Error borrowing item:', ['error' => $e->getMessage()]);
            return response()->json(['title' => 'Error', 'message' => 'Error borrowing item!'], 500);
        }
    }
}
