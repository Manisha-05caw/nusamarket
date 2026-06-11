<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class CartItem extends Model
{
    use HasUuids;
    public $timestamps = false;

    protected $fillable = ['cart_id','variant_id','quantity'];

    public function cart()    { return $this->belongsTo(Cart::class); }
    public function variant() { return $this->belongsTo(ProductVariant::class); }

    public function getSubtotalAttribute(): float
    {
        return $this->variant->price * $this->quantity;
    }
}
