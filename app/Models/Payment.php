<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Payment extends Model
{
    use HasUuids;

    protected $fillable = [
        'order_id','method','gateway','gateway_ref','gateway_payload','amount','status','paid_at','expired_at',
    ];

    protected $casts = [
        'gateway_payload' => 'array',
        'amount'          => 'float',
        'paid_at'         => 'datetime',
        'expired_at'      => 'datetime',
    ];

    public function order() { return $this->belongsTo(Order::class); }
    public function isPaid(): bool    { return $this->status === 'paid'; }
    public function isPending(): bool { return $this->status === 'pending'; }
}
