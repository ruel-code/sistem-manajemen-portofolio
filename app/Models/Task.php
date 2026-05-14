<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id', 'workspace_id', 'parent_id', 'title', 'description',
        'status', 'priority', 'order', 'due_date', 'started_at', 'completed_at',
        'estimated_hours', 'actual_hours', 'assigned_to', 'created_by',
    ];

    protected $casts = [
        'due_date' => 'date',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /** Priority badge colors */
    public static $priorityColors = [
        'low' => 'slate',
        'medium' => 'blue',
        'high' => 'orange',
        'urgent' => 'red',
    ];

    /** Kanban status columns */
    public static $statusColumns = [
        'todo' => 'To Do',
        'in_progress' => 'In Progress',
        'review' => 'Review',
        'done' => 'Done',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    public function parent()
    {
        return $this->belongsTo(Task::class, 'parent_id');
    }

    public function subtasks()
    {
        return $this->hasMany(Task::class, 'parent_id');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function comments()
    {
        return $this->hasMany(TaskComment::class)->whereNull('parent_id')->latest();
    }

    public function checklists()
    {
        return $this->hasMany(TaskChecklist::class)->orderBy('order');
    }

    public function attachments()
    {
        return $this->hasMany(TaskAttachment::class);
    }

    public function labels()
    {
        return $this->belongsToMany(TaskLabel::class, 'task_label_pivot');
    }

    /** Checklist completion percentage */
    public function getChecklistProgressAttribute(): int
    {
        $total = $this->checklists()->count();
        if ($total === 0) return 0;
        $done = $this->checklists()->where('is_completed', true)->count();
        return (int) round(($done / $total) * 100);
    }

    /** Is overdue */
    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date && $this->due_date->isPast() && $this->status !== 'done';
    }
}
