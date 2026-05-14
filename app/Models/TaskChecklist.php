<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskChecklist extends Model
{
    protected $fillable = ['task_id', 'title', 'is_completed', 'order', 'completed_by', 'completed_at'];

    protected $casts = ['is_completed' => 'boolean', 'completed_at' => 'datetime'];

    public function task() { return $this->belongsTo(Task::class); }
    public function completedBy() { return $this->belongsTo(User::class, 'completed_by'); }
}
