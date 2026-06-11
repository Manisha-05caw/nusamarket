<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Product extends Model
{
    use HasUuids;

    const CATEGORIES = [
        'fashion_wanita' => 'Fashion Wanita',
        'fashion_pria'   => 'Fashion Pria',
        'elektronik'     => 'Elektronik',
        'rumah_dapur'    => 'Rumah & Dapur',
        'kecantikan'     => 'Kecantikan',
        'olahraga'       => 'Olahraga',
        'otomotif'       => 'Otomotif',
        'mainan'         => 'Mainan & Hobi',
        'buku'           => 'Buku & Alat Tulis',
        'lainnya'        => 'Lainnya',
    ];

    protected $fillable = [
        'store_id','name','slug','description','category',
        'base_price','discount_percent','weight_gram',
        'rating_avg','total_reviews','total_sold','is_active',
    ];

    protected $casts = [
        'base_price'       => 'float',
        'rating_avg'       => 'float',
        'is_active'        => 'boolean',
        'discount_percent' => 'integer',
    ];

    public function store()    { return $this->belongsTo(Store::class); }
    public function variants() { return $this->hasMany(ProductVariant::class); }
    public function images()   { return $this->hasMany(ProductImage::class)->orderBy('sort_order'); }
    public function reviews()  { return $this->hasMany(Review::class); }
    public function wishlists(){ return $this->hasMany(Wishlist::class); }

    public function getDisplayPriceAttribute(): float
    {
        return $this->discount_percent > 0
            ? $this->base_price * (1 - $this->discount_percent / 100)
            : $this->base_price;
    }

    public function getThumbnailAttribute(): ?string
    {
        return $this->images->first()?->url;
    }

    public function getIsNewAttribute(): bool
    {
        return $this->created_at->diffInDays(now()) <= 14;
    }

    public function getRouteKeyName(): string { return 'slug'; }
}
