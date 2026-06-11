<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Wishlist extends Model
{
    use HasUuids;
    public $timestamps = false;
    const CREATED_AT = 'created_at';
    protected $fillable = ['user_id','product_id'];
    protected $casts = ['created_at' => 'datetime'];

    public function user()    { return $this->belongsTo(User::class); }
    public function product() { return $this->belongsTo(Product::class); }
}
