<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasUuids, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'phone',
        'avatar_url', 'role', 'is_verified', 'is_active',
        'last_login_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'is_verified'       => 'boolean',
        'is_active'         => 'boolean',
        'email_verified_at' => 'datetime',
        'last_login_at'     => 'datetime',
    ];

    public function stores()        { return $this->hasMany(Store::class, 'owner_id'); }
    public function orders()        { return $this->hasMany(Order::class, 'buyer_id'); }
    public function addresses()     { return $this->hasMany(Address::class); }
    public function cart()          { return $this->hasOne(Cart::class); }
    public function wishlists()     { return $this->hasMany(Wishlist::class); }
    public function reviews()       { return $this->hasMany(Review::class, 'buyer_id'); }
    public function conversations() { return $this->hasMany(Conversation::class, 'buyer_id'); }
    public function notifications() { return $this->hasMany(Notification::class); }

    public function getUnreadNotificationsCountAttribute(): int
    {
        return $this->notifications()->where('is_read', false)->count();
    }

    public function getCartCountAttribute(): int
    {
        return $this->cart?->items()->sum('quantity') ?? 0;
    }
}