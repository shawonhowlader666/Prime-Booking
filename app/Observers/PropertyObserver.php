<?php

namespace App\Observers;

use App\Models\Property;
use App\Repositories\PropertyRepository;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

/**
 * PropertyObserver
 * ────────────────
 * Automatically fires on every Property model event.
 * 1. Clears Redis cache whenever a property is created/updated/deleted
 * 2. Logs admin actions to activity_logs table for audit trail
 */
class PropertyObserver
{
    public function __construct(
        protected PropertyRepository $repo
    ) {}

    /** Called after a property is created */
    public function created(Property $property): void
    {
        $this->repo->clearPropertyCache($property);
        $this->log('created', $property, "Created property: {$property->name}");
    }

    /** Called after a property is updated */
    public function updated(Property $property): void
    {
        $this->repo->clearPropertyCache($property);

        // Log which fields changed
        $changed = collect($property->getChanges())
            ->except(['updated_at'])
            ->keys()
            ->implode(', ');

        $this->log('updated', $property, "Updated property [{$property->name}]: changed [{$changed}]");
    }

    /** Called after a property is deleted */
    public function deleted(Property $property): void
    {
        $this->repo->clearPropertyCache($property);
        $this->log('deleted', $property, "Deleted property: {$property->name} (ID {$property->id})");
    }

    /** Write audit log safely */
    private function log(string $action, Property $property, string $description): void
    {
        try {
            ActivityLog::create([
                'user_id'      => Auth::id(),
                'user_name'    => Auth::user()?->name ?? 'System',
                'action'       => $action,
                'model_type'   => 'Property',
                'model_id'     => $property->id,
                'description'  => $description,
                'ip_address'   => request()?->ip(),
                'user_agent'   => request()?->userAgent(),
            ]);
        } catch (\Exception $e) {
            // Silently fail if activity_logs table doesn't exist yet
        }
    }
}
