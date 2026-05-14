<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name', 'email', 'password', 'avatar', 'phone',
        'job_title', 'bio', 'timezone', 'theme', 'is_active',
        'last_login_at', 'email_verified_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    /** Workspaces yang dimiliki user */
    public function ownedWorkspaces()
    {
        return $this->hasMany(Workspace::class, 'owner_id');
    }

    /** Workspaces yang diikuti user */
    public function workspaces()
    {
        return $this->belongsToMany(Workspace::class, 'workspace_members')
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }

    /** Tasks yang di-assign ke user */
    public function assignedTasks()
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    /** Projects yang di-manage user */
    public function managedProjects()
    {
        return $this->hasMany(Project::class, 'manager_id');
    }

    /** Online status */
    public function onlineStatus()
    {
        return $this->hasOne(UserOnlineStatus::class);
    }

    /** Check if user is online (last seen within 5 minutes) */
    public function isOnline(): bool
    {
        return $this->onlineStatus && $this->onlineStatus->is_online;
    }

    /** Get avatar URL */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        $initial = urlencode(substr($this->name, 0, 2));
        return "https://ui-avatars.com/api/?name={$initial}&background=6366f1&color=fff&size=128";
    }
}
