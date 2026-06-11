<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ProductImage extends Model
{
    use HasUuids;
    public $timestamps = false;
    protected $fillable = ['product_id','url','alt_text','sort_order'];
    public function product() { return $this->belongsTo(Product::class); }
}
