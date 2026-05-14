<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Workspace extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'description', 'logo', 'color',
        'owner_id', 'plan', 'trial_ends_at', 'settings',
    ];

    protected $casts = [
        'settings' => 'array',
        'trial_ends_at' => 'datetime',
    ];

    /** Auto-generate slug dari name */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($workspace) {
            if (empty($workspace->slug)) {
                $workspace->slug = Str::slug($workspace->name) . '-' . Str::random(6);
            }
        });
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'workspace_members')
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function chatChannels()
    {
        return $this->hasMany(ChatChannel::class);
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /** Get logo URL */
    public function getLogoUrlAttribute(): string
    {
        if ($this->logo) {
            return asset('storage/' . $this->logo);
        }
        return "https://ui-avatars.com/api/?name=" . urlencode($this->name) . "&background=6366f1&color=fff&size=128&bold=true";
    }

    /** Count active projects */
    public function getActiveProjectsCountAttribute(): int
    {
        return $this->projects()->where('status', 'active')->count();
    }
}
