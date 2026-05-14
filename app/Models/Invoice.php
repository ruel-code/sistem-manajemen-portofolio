<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'workspace_id', 'project_id', 'client_id', 'invoice_number', 'status',
        'subtotal', 'tax', 'discount', 'total', 'currency', 'items', 'notes',
        'issue_date', 'due_date', 'paid_at', 'payment_method', 'payment_reference',
    ];

    protected $casts = [
        'items' => 'array',
        'issue_date' => 'date',
        'due_date' => 'date',
        'paid_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public static $statusColors = [
        'draft' => 'slate',
        'sent' => 'blue',
        'paid' => 'green',
        'overdue' => 'red',
        'cancelled' => 'gray',
    ];

    public function workspace() { return $this->belongsTo(Workspace::class); }
    public function project() { return $this->belongsTo(Project::class); }
    public function client() { return $this->belongsTo(User::class, 'client_id'); }

    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->total, 0, ',', '.');
    }

    /** Generate invoice number */
    public static function generateNumber(): string
    {
        $last = static::latest()->first();
        $number = $last ? intval(substr($last->invoice_number, -4)) + 1 : 1;
        return 'INV-' . date('Ymd') . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}
