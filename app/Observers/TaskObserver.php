<?php

namespace App\Observers;

use App\Models\Task;
use App\Services\DashboardMetricsService;
use App\Services\ProjectProgressService;

class TaskObserver
{
    /**
     * Create the observer.
     */
    public function __construct(
        protected DashboardMetricsService $metricsService,
        protected ProjectProgressService $progressService
    ) {}

    /**
     * Handle the Task "created" event.
     */
    public function created(Task $task): void
    {
        $this->updateProjectProgress($task);
        $this->invalidateTaskMetrics();
    }

    /**
     * Handle the Task "updated" event.
     */
    public function updated(Task $task): void
    {
        // Only recalculate if relevant fields changed
        if ($task->wasChanged(['status', 'progress', 'estimate_hours'])) {
            $this->updateProjectProgress($task);
        }

        // Only invalidate if status, due_date, or priority changed
        if ($task->wasChanged(['status', 'due_date', 'priority'])) {
            $this->invalidateTaskMetrics();
        }
    }

    /**
     * Handle the Task "deleted" event.
     */
    public function deleted(Task $task): void
    {
        $this->updateProjectProgress($task);
        $this->invalidateTaskMetrics();
    }

    /**
     * Handle the Task "restored" event.
     */
    public function restored(Task $task): void
    {
        $this->updateProjectProgress($task);
        $this->invalidateTaskMetrics();
    }

    /**
     * Update project progress when task changes
     */
    protected function updateProjectProgress(Task $task): void
    {
        if ($task->project) {
            try {
                $this->progressService->calculateProgress($task->project, true);
            } catch (\Exception $e) {
                \Log::error("Failed to update project progress for task {$task->id}: {$e->getMessage()}");
            }
        }
    }

    /**
     * Invalidate task-related metrics
     */
    protected function invalidateTaskMetrics(): void
    {
        $this->metricsService->invalidateMetrics([
            'active_tasks',
            'completion_rate',
            'overdue_tasks',
            'pending_reviews',
        ]);
    }
}
