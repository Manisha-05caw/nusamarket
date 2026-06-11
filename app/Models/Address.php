<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Address extends Model
{
    use HasUuids;
    public $timestamps = false;
    const CREATED_AT = 'created_at';

    protected $fillable = [
        'user_id','label','recipient','phone','address_line','city','province','postal_code','is_default',
    ];

    protected $casts = ['is_default' => 'boolean', 'created_at' => 'datetime'];

    public function user() { return $this->belongsTo(User::class); }

    public function getFullAddressAttribute(): string
    {
        return "{$this->address_line}, {$this->city}, {$this->province} {$this->postal_code}";
    }
}
