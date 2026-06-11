<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ProductVariant extends Model
{
    use HasUuids;

    protected $fillable = ['product_id','size','color','sku','price','stock','is_active'];
    protected $casts = ['price' => 'float', 'stock' => 'integer', 'is_active' => 'boolean'];

    public function product()    { return $this->belongsTo(Product::class); }
    public function cartItems()  { return $this->hasMany(CartItem::class); }
    public function orderItems() { return $this->hasMany(OrderItem::class); }

    public function getDisplayNameAttribute(): string
    {
        return collect([$this->size, $this->color])->filter()->implode(' / ');
    }
}
