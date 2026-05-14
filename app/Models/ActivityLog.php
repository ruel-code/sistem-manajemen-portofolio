<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id', 'workspace_id', 'action', 'model_type', 'model_id',
        'description', 'properties', 'ip_address', 'user_agent',
    ];

    protected $casts = ['properties' => 'array'];

    public function user() { return $this->belongsTo(User::class); }
    public function workspace() { return $this->belongsTo(Workspace::class); }
}
