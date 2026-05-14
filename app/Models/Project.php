<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'workspace_id', 'name', 'slug', 'description', 'thumbnail', 'color',
        'status', 'priority', 'progress', 'start_date', 'due_date', 'budget',
        'manager_id', 'client_id', 'created_by', 'settings',
    ];

    protected $casts = [
        'start_date' => 'date',
        'due_date' => 'date',
        'budget' => 'decimal:2',
        'settings' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($project) {
            if (empty($project->slug)) {
                $project->slug = Str::slug($project->name) . '-' . Str::random(6);
            }
        });
    }

    /** Status badge colors */
    public static $statusColors = [
        'planning' => 'blue',
        'active' => 'green',
        'review' => 'yellow',
        'completed' => 'purple',
        'on_hold' => 'red',
    ];

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'project_members')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function activities()
    {
        return $this->hasMany(ProjectActivity::class)->latest();
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /** Auto-calculate progress based on tasks */
    public function calculateProgress(): int
    {
        $total = $this->tasks()->count();
        if ($total === 0) return 0;
        $done = $this->tasks()->where('status', 'done')->count();
        return (int) round(($done / $total) * 100);
    }

    /** Get deadline status */
    public function getDeadlineStatusAttribute(): string
    {
        if (!$this->due_date) return 'no_deadline';
        if ($this->status === 'completed') return 'completed';
        if ($this->due_date->isPast()) return 'overdue';
        if ($this->due_date->diffInDays(now()) <= 7) return 'near';
        return 'normal';
    }

    public function getThumbnailUrlAttribute(): string
    {
        if ($this->thumbnail) {
            return asset('storage/' . $this->thumbnail);
        }
        return '';
    }
}
