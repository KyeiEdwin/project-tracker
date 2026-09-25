<?php

namespace App\Listeners;

use App\Events\ProjectProgressUpdated;
use App\Services\DashboardMetricsService;
use Illuminate\Contracts\Queue\ShouldQueue;

class InvalidateProjectMetricsCache implements ShouldQueue
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
    public function handle(ProjectProgressUpdated $event): void
    {
        // Invalidate metrics that depend on project data
        $this->metricsService->invalidateMetrics([
            'total_projects',
            'completion_rate',
            'budget_used',
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
