<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class ItemController extends Controller
{
    public function index()
    {
        $keys = Item::all()->sortBy(function ($item) {
            // Split the name into alphabetical and numerical parts
            preg_match('/^([^\d]*)(\d*)$/', $item->name, $matches);
    
            $alphabeticalPart = $matches[1] ?? ''; // Part before the number
            $numericPart = isset($matches[2]) && $matches[2] !== '' ? (int)$matches[2] : PHP_INT_MAX;
    
            // Return a tuple for sorting: alphabetical part first, then numerical part
            return [$alphabeticalPart, $numericPart];
        });
    
        // Paginate the sorted collection manually
        $currentPage = request()->get('page', 1); // Get current page or default to 1
        $perPage = 50; // Number of items per page
        $paginatedKeys = new \Illuminate\Pagination\LengthAwarePaginator(
            $keys->slice(($currentPage - 1) * $perPage, $perPage), // Slice the collection
            $keys->count(), // Total items
            $perPage, // Items per page
            $currentPage, // Current page
            ['path' => request()->url(), 'query' => request()->query()] // Preserve query string
        );
    
        return view('key.key', ['keys' => $paginatedKeys]);
    }    

    public function add(Request $request)
    {   
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'type' => 'required',
        ]);

        if ($validator->fails()) {
            session()->flash('message', 'Please check your data');
            session()->flash('color', 'error');
            return redirect(route('key.view'));
        }

        // Create a new item
        $key = new Item();   
        $key->name = $request->input('name');
        $key->type = $request->input('type');
        $key->save();

        session()->flash('message', 'Item added successfully');
        session()->flash('color', 'success');
        return redirect(route('key.view'));
    }

    public function update(Request $request)
    {
        $decrypted = Crypt::decryptString($request->id);
    
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'type' => 'required',
        ]);
    
        if ($validator->fails()) {
            session()->flash('message', 'Please check your data');
            session()->flash('color', 'error');
            return redirect(route('key.view'));
        }
    
        $key = Item::findOrFail($decrypted);
    
        if ($key) {
            $key->name = $request->name; 
            $key->type = $request->type; 
            $key->save(); 
    
            session()->flash('message', 'Item updated successfully');
            session()->flash('color', 'success');
            return redirect(route('key.view'));
        } else {
            session()->flash('message', 'Cannot update data');
            session()->flash('color', 'error');
            return redirect(route('key.view'));
        }
    }

    public function delete(Request $request)
    {
        $decrypted = Crypt::decryptString($request->id);

        $delete = Item::where('id', $decrypted)->delete();

        if ($delete) {
            session()->flash('message', 'Item deleted successfully!');
            session()->flash('color', 'success');
            return redirect(route('key.view'));
        }
        session()->flash('message', 'Error deleting item!');
        session()->flash('color', 'error');
        return redirect(route('key.view'));
    }
}
