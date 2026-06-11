<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class OrderItem extends Model
{
    use HasUuids;
    public $timestamps = false;
    const CREATED_AT = 'created_at';

    protected $fillable = [
        'order_id','variant_id','store_id','product_id','product_name',
        'variant_info','quantity','unit_price','subtotal','item_status',
    ];

    protected $casts = ['variant_info' => 'array', 'unit_price' => 'float', 'subtotal' => 'float', 'created_at' => 'datetime'];

    public function order()   { return $this->belongsTo(Order::class); }
    public function variant() { return $this->belongsTo(ProductVariant::class); }
    public function product() { return $this->belongsTo(Product::class); }
    public function store()   { return $this->belongsTo(Store::class); }
    public function review()  { return $this->hasOne(Review::class); }
    public function hasReview(): bool { return $this->review()->exists(); }
}
