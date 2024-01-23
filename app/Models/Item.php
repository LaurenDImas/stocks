<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Item extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function stockOpname()
    {
        return $this->hasOne(StockOpname::class);
    }

    public function storeStocks()
    {
        return $this->hasMany(StoreStock::class)->where('store_id', Auth::user()->store_id);
    }
}
