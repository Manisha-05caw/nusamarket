<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Order extends Model
{
    use HasUuids;

    protected $fillable = [
        'buyer_id','status','subtotal','shipping_cost','platform_fee','total_amount',
        'shipping_address','courier','courier_service','tracking_number','notes','paid_at','completed_at',
    ];

    protected $casts = [
        'shipping_address' => 'array',
        'paid_at'          => 'datetime',
        'completed_at'     => 'datetime',
        'subtotal'         => 'float',
        'shipping_cost'    => 'float',
        'platform_fee'     => 'float',
        'total_amount'     => 'float',
    ];

    const STATUS_LABELS = [
        'pending_payment' => 'Menunggu Pembayaran',
        'paid'            => 'Sudah Dibayar',
        'processing'      => 'Diproses Penjual',
        'shipped'         => 'Dalam Pengiriman',
        'delivered'       => 'Sudah Diterima',
        'completed'       => 'Selesai',
        'cancelled'       => 'Dibatalkan',
        'refunded'        => 'Dikembalikan',
    ];

    public function buyer()   { return $this->belongsTo(User::class, 'buyer_id'); }
    public function items()   { return $this->hasMany(OrderItem::class); }
    public function payment() { return $this->hasOne(Payment::class); }

    public function scopeForBuyer($q, $userId) { return $q->where('buyer_id', $userId); }
    public function scopeStatus($q, $status)   { return $q->where('status', $status); }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending_payment', 'paid']);
    }
}
