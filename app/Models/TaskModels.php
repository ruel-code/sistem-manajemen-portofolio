<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskComment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['task_id', 'user_id', 'content', 'parent_id'];

    public function task() { return $this->belongsTo(Task::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function replies() { return $this->hasMany(TaskComment::class, 'parent_id')->latest(); }
}

// ===========================

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskChecklist extends Model
{
    protected $fillable = ['task_id', 'title', 'is_completed', 'order', 'completed_by', 'completed_at'];

    protected $casts = ['is_completed' => 'boolean', 'completed_at' => 'datetime'];

    public function task() { return $this->belongsTo(Task::class); }
    public function completedBy() { return $this->belongsTo(User::class, 'completed_by'); }
}

// ===========================

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskAttachment extends Model
{
    protected $fillable = ['task_id', 'user_id', 'name', 'path', 'mime_type', 'size'];

    public function task() { return $this->belongsTo(Task::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function getUrlAttribute(): string { return asset('storage/' . $this->path); }
}

// ===========================

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskLabel extends Model
{
    protected $fillable = ['workspace_id', 'name', 'color'];

    public function tasks() { return $this->belongsToMany(Task::class, 'task_label_pivot'); }
}
