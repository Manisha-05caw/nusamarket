<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Notification extends Model
{
    use HasUuids;
    public $timestamps = false;
    const CREATED_AT = 'created_at';

    protected $fillable = ['user_id','type','title','body','data','is_read','read_at'];
    protected $casts = ['data' => 'array', 'is_read' => 'boolean', 'read_at' => 'datetime', 'created_at' => 'datetime'];

    public function user() { return $this->belongsTo(User::class); }
    public function markAsRead(): void { $this->update(['is_read' => true, 'read_at' => now()]); }
}
