<?php

namespace App\Observers;

use App\Models\Project;
use App\Services\DashboardMetricsService;

class ProjectObserver
{
    /**
     * Create the observer.
     */
    public function __construct(
        protected DashboardMetricsService $metricsService
    ) {}

    /**
     * Handle the Project "created" event.
     */
    public function created(Project $project): void
    {
        $this->invalidateProjectMetrics();
    }

    /**
     * Handle the Project "updated" event.
     */
    public function updated(Project $project): void
    {
        // Only invalidate if relevant fields changed
        if ($project->wasChanged(['status', 'budget', 'spent', 'progress'])) {
            $this->invalidateProjectMetrics();
        }
    }

    /**
     * Handle the Project "deleted" event.
     */
    public function deleted(Project $project): void
    {
        $this->invalidateProjectMetrics();
    }

    /**
     * Handle the Project "restored" event.
     */
    public function restored(Project $project): void
    {
        $this->invalidateProjectMetrics();
    }

    /**
     * Invalidate project-related metrics
     */
    protected function invalidateProjectMetrics(): void
    {
        $this->metricsService->invalidateMetrics([
            'total_projects',
            'completion_rate',
            'budget_used',
        ]);
    }
}
