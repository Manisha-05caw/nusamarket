<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Review extends Model
{
    use HasUuids;

    protected $fillable = [
        'order_item_id','buyer_id','product_id','store_id',
        'rating_product','rating_delivery','rating_service',
        'comment','seller_reply','replied_at','is_flagged',
    ];

    protected $casts = ['is_flagged' => 'boolean', 'replied_at' => 'datetime'];

    public function orderItem() { return $this->belongsTo(OrderItem::class); }
    public function buyer()     { return $this->belongsTo(User::class, 'buyer_id'); }
    public function product()   { return $this->belongsTo(Product::class); }
    public function store()     { return $this->belongsTo(Store::class); }
    public function images()    { return $this->hasMany(ReviewImage::class)->orderBy('sort_order'); }
}
