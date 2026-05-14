<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class File extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'workspace_id', 'project_id', 'user_id', 'name', 'original_name',
        'path', 'mime_type', 'size', 'folder', 'is_public', 'download_count',
    ];

    protected $casts = ['is_public' => 'boolean'];

    public function workspace() { return $this->belongsTo(Workspace::class); }
    public function project() { return $this->belongsTo(Project::class); }
    public function user() { return $this->belongsTo(User::class); }

    public function getUrlAttribute(): string { return asset('storage/' . $this->path); }

    public function getFormattedSizeAttribute(): string
    {
        $size = $this->size;
        if ($size < 1024) return $size . ' B';
        if ($size < 1048576) return round($size / 1024, 1) . ' KB';
        if ($size < 1073741824) return round($size / 1048576, 1) . ' MB';
        return round($size / 1073741824, 1) . ' GB';
    }

    public function getIconAttribute(): string
    {
        $mime = $this->mime_type;
        if (str_contains($mime, 'image')) return 'image';
        if (str_contains($mime, 'pdf')) return 'pdf';
        if (str_contains($mime, 'video')) return 'video';
        if (str_contains($mime, 'audio')) return 'audio';
        if (str_contains($mime, 'zip') || str_contains($mime, 'rar')) return 'archive';
        if (str_contains($mime, 'word') || str_contains($mime, 'document')) return 'word';
        if (str_contains($mime, 'excel') || str_contains($mime, 'spreadsheet')) return 'excel';
        return 'file';
    }
}
