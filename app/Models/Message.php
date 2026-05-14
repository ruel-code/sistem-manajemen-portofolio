<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use SoftDeletes;

    protected $fillable = ['chat_channel_id', 'user_id', 'content', 'type', 'attachments', 'reply_to'];

    protected $casts = ['attachments' => 'array'];

    public function channel() { return $this->belongsTo(ChatChannel::class, 'chat_channel_id'); }
    public function user() { return $this->belongsTo(User::class); }
    public function replyTo() { return $this->belongsTo(Message::class, 'reply_to'); }
}
