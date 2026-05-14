<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskAttachment extends Model
{
    protected $fillable = ['task_id', 'user_id', 'name', 'path', 'mime_type', 'size'];

    public function task() { return $this->belongsTo(Task::class); }
    public function user() { return $this->belongsTo(User::class); }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->path);
    }

    public function getFormattedSizeAttribute(): string
    {
        $size = $this->size;
        if ($size < 1024) return $size . ' B';
        if ($size < 1048576) return round($size / 1024, 1) . ' KB';
        return round($size / 1048576, 1) . ' MB';
    }
}
