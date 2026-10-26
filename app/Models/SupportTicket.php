<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportTicket extends Model
{
    protected $fillable = [
        'user_id', 'ticket_number', 'subject', 'category',
        'message', 'status', 'priority', 'source',
        'assigned_to', 'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    // ── Relationships ───────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(SupportTicketReply::class, 'ticket_id')->orderBy('created_at');
    }

    // ── Helpers ────────────────────────────────────────────────

    /** Generate next unique ticket number e.g. TKT-2026-00042 */
    public static function generateTicketNumber(): string
    {
        $year  = now()->year;
        $last  = static::whereYear('created_at', $year)->lockForUpdate()->count();
        return 'TKT-' . $year . '-' . str_pad($last + 1, 5, '0', STR_PAD_LEFT);
    }

    public function isOpen(): bool
    {
        return in_array($this->status, ['open', 'in_progress']);
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'open'        => 'bg-warning text-dark',
            'in_progress' => 'bg-primary text-white',
            'resolved'    => 'bg-success text-white',
            'closed'      => 'bg-secondary text-white',
            default       => 'bg-light text-dark',
        };
    }

    public function priorityColor(): string
    {
        return match ($this->priority) {
            'urgent' => '#ea5455',
            'high'   => '#ff9f43',
            'medium' => '#1890ff',
            'low'    => '#28c76f',
            default  => '#94a3b8',
        };
    }
}
