<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatChannel extends Model
{

    protected $fillable = ['workspace_id', 'name', 'type', 'description', 'created_by'];

    public function workspace() { return $this->belongsTo(Workspace::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }

    public function members()
    {
        return $this->belongsToMany(User::class, 'chat_channel_members')
            ->withPivot('last_read_at')
            ->withTimestamps();
    }

    public function messages()
    {
        return $this->hasMany(Message::class)->latest();
    }

    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latest();
    }
}
