<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ActivityLog — Audit trail for all admin/vendor actions
 */
class ActivityLog extends Model
{
    protected $fillable = [
        'user_id', 'user_name', 'action', 'model_type',
        'model_id', 'description', 'ip_address', 'user_agent',
    ];

    public $timestamps = true;

    // Only created_at matters for logs
    const UPDATED_AT = null;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Human-readable action badge color */
    public function getActionColorAttribute(): string
    {
        return match($this->action) {
            'created' => '#28c76f',
            'updated' => '#1890ff',
            'deleted' => '#ea5455',
            'login'   => '#7367f0',
            'logout'  => '#8c8c8c',
            default   => '#ff9f43',
        };
    }

    /** Icon for action */
    public function getActionIconAttribute(): string
    {
        return match($this->action) {
            'created' => 'fa-plus-circle',
            'updated' => 'fa-pen',
            'deleted' => 'fa-trash',
            'login'   => 'fa-right-to-bracket',
            'logout'  => 'fa-right-from-bracket',
            default   => 'fa-circle-dot',
        };
    }
}
