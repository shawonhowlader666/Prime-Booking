<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class AuditService
{
    /**
     * Record an immutable audit log entry for compliance, security, and traceability.
     */
    public static function log(
        int $hotelId,
        string $action,
        Model $entity,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?User $actor = null
    ): AuditLog {
        $actorId = $actor?->id ?? auth()->id() ?? null;
        $ip = Request::ip() ?? '127.0.0.1';
        $ua = Request::userAgent() ?? 'PMS Internal System/Console';

        return AuditLog::create([
            'hotel_id' => $hotelId,
            'user_id' => $actorId,
            'action' => strtoupper($action),
            'entity_type' => class_basename($entity),
            'entity_id' => $entity->getKey(),
            'old_values' => $oldValues,
            'new_values' => $newValues ?? $entity->toArray(),
            'ip_address' => $ip,
            'user_agent' => substr($ua, 0, 255),
            'created_at' => now(),
        ]);
    }
}
