<?php

namespace App\Listeners;

use App\Events\TaskUpdated;
use App\Services\DashboardMetricsService;
use Illuminate\Contracts\Queue\ShouldQueue;

class InvalidateTaskMetricsCache implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct(
        protected DashboardMetricsService $metricsService
    ) {}

    /**
     * Handle the event.
     */
    public function handle(TaskUpdated $event): void
    {
        // Invalidate metrics that depend on task data
        $this->metricsService->invalidateMetrics([
            'active_tasks',
            'completion_rate',
            'overdue_tasks',
            'pending_reviews',
        ]);
    }

    /**
     * Determine if the listener should be queued.
     */
    public function shouldQueue(): bool
    {
        return true;
    }
}
