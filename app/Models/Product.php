<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    // Other existing model methods and properties...

    /**
     * Get the borrower for the product.
     */
    public function borrower()
    {
        return $this->hasOne(Borrow::class, 'item_id', 'id')
            ->where('status', 'borrowed');
    }
}
