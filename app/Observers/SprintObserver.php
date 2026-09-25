<?php

namespace App\Observers;

use App\Models\Sprint;
use App\Services\SprintService;
use Illuminate\Support\Facades\Cache;

class SprintObserver
{
    public function __construct(
        protected SprintService $sprintService
    ) {}

    /**
     * Handle the Sprint "updating" event.
     * Enforce immutability for completed sprints.
     */
    public function updating(Sprint $sprint): void
    {
        // Prevent ANY modification to completed sprints
        if ($sprint->getOriginal('status') === 'completed') {
            throw new \RuntimeException(
                'Cannot modify completed sprint. Sprint data is immutable for historical accuracy.'
            );
        }
    }

    /**
     * Handle the Sprint "updated" event.
     */
    public function updated(Sprint $sprint): void
    {
        // Log status changes
        if ($sprint->wasChanged('status')) {
            $oldStatus = $sprint->getOriginal('status');
            $newStatus = $sprint->status;

            $eventType = match($newStatus) {
                'active' => 'sprint_started',
                'completed' => 'sprint_closed',
                default => 'status_updated'
            };

            $this->sprintService->logEvent($sprint, $eventType, [
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
            ]);
        }

        // Clear velocity cache for project when sprint status changes
        if ($sprint->wasChanged('status')) {
            Cache::forget("project_velocity_{$sprint->project_id}");
        }

        // Clear sprint metrics cache
        Cache::forget("sprint_metrics_{$sprint->id}");
        Cache::forget("sprint_burndown_{$sprint->id}");
    }

    /**
     * Handle the Sprint "deleting" event.
     * Prevent deletion of completed sprints.
     */
    public function deleting(Sprint $sprint): void
    {
        // Prevent soft-delete of completed sprints
        if ($sprint->status === 'completed') {
            throw new \RuntimeException(
                'Cannot delete completed sprint. Historical data must be preserved.'
            );
        }
    }

    /**
     * Handle the Sprint "deleted" event.
     */
    public function deleted(Sprint $sprint): void
    {
        // Clear caches
        Cache::forget("project_velocity_{$sprint->project_id}");
        Cache::forget("sprint_metrics_{$sprint->id}");
        Cache::forget("sprint_burndown_{$sprint->id}");
    }

    /**
     * Handle the Sprint "restored" event.
     */
    public function restored(Sprint $sprint): void
    {
        // Clear caches
        Cache::forget("project_velocity_{$sprint->project_id}");
    }
}
