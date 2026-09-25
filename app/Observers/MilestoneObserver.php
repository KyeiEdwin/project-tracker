<?php

namespace App\Observers;

use App\Models\Milestone;
use App\Services\DashboardMetricsService;

class MilestoneObserver
{
    /**
     * Create the observer.
     */
    public function __construct(
        protected DashboardMetricsService $metricsService
    ) {}

    /**
     * Handle the Milestone "created" event.
     */
    public function created(Milestone $milestone): void
    {
        $this->invalidateMilestoneMetrics();
    }

    /**
     * Handle the Milestone "updated" event.
     */
    public function updated(Milestone $milestone): void
    {
        // Only invalidate if status or due_date changed
        if ($milestone->wasChanged(['status', 'due_date'])) {
            $this->invalidateMilestoneMetrics();
        }
    }

    /**
     * Handle the Milestone "deleted" event.
     */
    public function deleted(Milestone $milestone): void
    {
        $this->invalidateMilestoneMetrics();
    }

    /**
     * Handle the Milestone "restored" event.
     */
    public function restored(Milestone $milestone): void
    {
        $this->invalidateMilestoneMetrics();
    }

    /**
     * Invalidate milestone-related metrics
     */
    protected function invalidateMilestoneMetrics(): void
    {
        $this->metricsService->invalidateMetric('milestones');
    }
}
