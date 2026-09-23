<?php

namespace App\Services;

use App\Models\Project;

class ProjectProgressService
{
    /**
     * Recalculate and persist the project's current task-based progress.
     */
    public function recalculate(Project $project): int
    {
        $tasks = $project->tasks()->get(['status', 'progress', 'estimate_hours']);

        if ($tasks->isEmpty()) {
            $progress = 0;
        } else {
            $estimatedHours = $tasks->sum(fn ($task) => (float) $task->estimate_hours);

            if ($estimatedHours > 0) {
                $weightedProgress = $tasks->sum(function ($task): float {
                    return (float) $task->estimate_hours * $this->effectiveProgress($task);
                });

                $progress = (int) round($weightedProgress / $estimatedHours);
            } else {
                $completedTasks = $tasks->whereIn('status', ['completed', 'done'])->count();
                $progress = (int) round(($completedTasks / $tasks->count()) * 100);
            }
        }

        $progress = max(0, min(100, $progress));
        $project->update(['progress' => $progress]);

        return $progress;
    }

    private function effectiveProgress(object $task): int
    {
        if (in_array($task->status, ['completed', 'done'], true)) {
            return 100;
        }

        return max(0, min(100, (int) $task->progress));
    }
}