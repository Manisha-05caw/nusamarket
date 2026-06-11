<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Store extends Model
{
    use HasUuids;

    protected $fillable = [
        'owner_id','name','slug','description','logo_url','banner_url',
        'city','province','rating_avg','total_reviews','total_sales','status',
    ];

    protected $casts = ['rating_avg' => 'float'];

    public function owner()        { return $this->belongsTo(User::class, 'owner_id'); }
    public function products()     { return $this->hasMany(Product::class); }
    public function orderItems()   { return $this->hasMany(OrderItem::class); }
    public function reviews()      { return $this->hasMany(Review::class); }
    public function conversations(){ return $this->hasMany(Conversation::class); }
    public function balance()      { return $this->hasOne(SellerBalance::class); }

    public function scopeActive($q) { return $q->where('status', 'active'); }
    public function getRouteKeyName(): string { return 'slug'; }
}
