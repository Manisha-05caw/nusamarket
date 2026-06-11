<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Conversation extends Model
{
    use HasUuids;
    public $timestamps = false;
    const CREATED_AT = 'created_at';

    protected $fillable = ['buyer_id','seller_id','store_id','product_id','last_message_at','buyer_unread','seller_unread'];
    protected $casts = ['last_message_at' => 'datetime', 'created_at' => 'datetime'];

    public function buyer()    { return $this->belongsTo(User::class, 'buyer_id'); }
    public function seller()   { return $this->belongsTo(User::class, 'seller_id'); }
    public function store()    { return $this->belongsTo(Store::class); }
    public function product()  { return $this->belongsTo(Product::class); }
    public function messages() { return $this->hasMany(Message::class)->orderBy('created_at'); }
    public function latestMessage() { return $this->hasOne(Message::class)->latestOfMany('created_at'); }

    public function getUnreadCountFor(string $userId): int
    {
        if ($this->buyer_id === $userId)  return $this->buyer_unread;
        if ($this->seller_id === $userId) return $this->seller_unread;
        return 0;
    }
}
