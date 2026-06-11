<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ReviewImage extends Model
{
    use HasUuids;
    public $timestamps = false;
    protected $fillable = ['review_id','url','sort_order'];
    public function review() { return $this->belongsTo(Review::class); }
}
