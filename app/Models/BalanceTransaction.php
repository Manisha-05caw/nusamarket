<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class BalanceTransaction extends Model
{
    use HasUuids;
    public $timestamps = false;
    const CREATED_AT = 'created_at';

    protected $fillable = ['store_id','order_id','type','amount','balance_after','description'];
    protected $casts = ['amount' => 'float', 'balance_after' => 'float', 'created_at' => 'datetime'];

    public function store() { return $this->belongsTo(Store::class); }
    public function order() { return $this->belongsTo(Order::class); }
}
