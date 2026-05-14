<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserOnlineStatus extends Model
{
    protected $fillable = ['user_id', 'last_seen_at', 'is_online'];

    protected $casts = [
        'is_online' => 'boolean',
        'last_seen_at' => 'datetime',
    ];

    public function user() { return $this->belongsTo(User::class); }
}
