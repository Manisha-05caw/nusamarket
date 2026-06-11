<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class SellerBalance extends Model
{
    use HasUuids;
    public $timestamps = false;
    const UPDATED_AT = 'updated_at';

    protected $fillable = ['store_id','available','pending','total_earned'];
    protected $casts = ['available' => 'float', 'pending' => 'float', 'total_earned' => 'float'];

    public function store() { return $this->belongsTo(Store::class); }
}
