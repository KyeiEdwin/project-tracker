<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ProjectProgressService
{
    /**
     * Calculate and update project progress
     */
    public function calculateProgress(Project $project, bool $update = true): int
    {
        // Choose calculation strategy based on project type
        $progress = match($project->project_type) {
            'agile' => $this->calculateAgileProgress($project),
            'predictive' => $this->calculatePredictiveProgress($project),
            'hybrid' => $this->calculateHybridProgress($project),
            default => $this->calculateWeightedProgress($project),
        };

        // Ensure progress is between 0 and 100
        $progress = max(0, min(100, $progress));

        // Update project if requested
        if ($update && $project->progress !== $progress) {
            $project->update(['progress' => $progress]);
            
            // Dispatch event for other services to react
            event('project.progress.updated', [$project, $progress]);
        }

        return $progress;
    }

    /**
     * Strategy 2: Weighted by effort (DEFAULT & RECOMMENDED)
     */
    protected function calculateWeightedProgress(Project $project): int
    {
        $tasks = $project->tasks()
            ->select(['id', 'status', 'estimate_hours', 'progress'])
            ->get();

        if ($tasks->isEmpty()) {
            return 0;
        }

        // Check if any tasks have estimates
        $hasEstimates = $tasks->whereNotNull('estimate_hours')->where('estimate_hours', '>', 0)->isNotEmpty();

        if ($hasEstimates) {
            return $this->calculateByEstimatedHours($tasks);
        }

        // Fallback: simple task count
        return $this->calculateByTaskCount($tasks);
    }

    /**
     * Calculate progress by estimated hours
     */
    protected function calculateByEstimatedHours($tasks): int
    {
        $totalHours = 0;
        $completedHours = 0;

        foreach ($tasks as $task) {
            $hours = $task->estimate_hours ?? 1; // Default 1 hour if missing
            $totalHours += $hours;

            if ($this->isTaskCompleted($task)) {
                $completedHours += $hours;
            } elseif ($task->status === 'in-progress' && $task->progress > 0) {
                // Partial credit for in-progress tasks based on their individual progress
                $completedHours += ($hours * ($task->progress / 100));
            }
        }

        if ($totalHours == 0) {
            return 0;
        }

        return (int) round(($completedHours / $totalHours) * 100);
    }

    /**
     * Calculate progress by simple task count (Fallback)
     */
    protected function calculateByTaskCount($tasks): int
    {
        $total = $tasks->count();
        $completed = $tasks->filter(fn($task) => $this->isTaskCompleted($task))->count();

        if ($total == 0) {
            return 0;
        }

        return (int) round(($completed / $total) * 100);
    }

    /**
     * Calculate agile progress (milestone-based)
     */
    protected function calculateAgileProgress(Project $project): int
    {
        $milestones = $project->milestones()->get();

        if ($milestones->isEmpty()) {
            // Fallback to task-based if no milestones
            return $this->calculateWeightedProgress($project);
        }

        $total = $milestones->count();
        $completed = $milestones->where('status', 'completed')->count();

        return (int) round(($completed / $total) * 100);
    }

    /**
     * Calculate predictive progress (detailed task tracking)
     */
    protected function calculatePredictiveProgress(Project $project): int
    {
        // For predictive, use strict weighted calculation
        return $this->calculateWeightedProgress($project);
    }

    /**
     * Calculate hybrid progress (70% tasks + 30% milestones)
     */
    protected function calculateHybridProgress(Project $project): int
    {
        $taskProgress = $this->calculateWeightedProgress($project);
        $milestoneProgress = $this->calculateMilestoneProgress($project);

        // Weighted average: 70% task, 30% milestone
        return (int) round(($taskProgress * 0.7) + ($milestoneProgress * 0.3));
    }

    /**
     * Calculate milestone completion percentage
     */
    protected function calculateMilestoneProgress(Project $project): int
    {
        $milestones = $project->milestones()->get();

        if ($milestones->isEmpty()) {
            return 0;
        }

        $total = $milestones->count();
        $completed = $milestones->where('status', 'completed')->count();

        return (int) round(($completed / $total) * 100);
    }

    /**
     * Calculate priority-weighted progress
     */
    public function calculatePriorityWeightedProgress(Project $project): int
    {
        $tasks = $project->tasks()
            ->select(['id', 'status', 'priority'])
            ->get();

        if ($tasks->isEmpty()) {
            return 0;
        }

        $weights = [
            'critical' => 4,
            'high' => 3,
            'medium' => 2,
            'low' => 1,
        ];

        $totalWeight = 0;
        $completedWeight = 0;

        foreach ($tasks as $task) {
            $weight = $weights[$task->priority] ?? 2; // Default to medium
            $totalWeight += $weight;

            if ($this->isTaskCompleted($task)) {
                $completedWeight += $weight;
            }
        }

        if ($totalWeight == 0) {
            return 0;
        }

        return (int) round(($completedWeight / $totalWeight) * 100);
    }

    /**
     * Check if task is completed
     */
    protected function isTaskCompleted(Task $task): bool
    {
        return in_array($task->status, ['completed', 'done']);
    }

    /**
     * Recalculate progress for multiple projects
     */
    public function recalculateAll(array $projectIds = null): int
    {
        $query = Project::query();

        if ($projectIds) {
            $query->whereIn('id', $projectIds);
        }

        $updated = 0;

        $query->chunk(100, function ($projects) use (&$updated) {
            foreach ($projects as $project) {
                try {
                    $this->calculateProgress($project, true);
                    $updated++;
                } catch (\Exception $e) {
                    \Log::error("Failed to calculate progress for project {$project->id}: {$e->getMessage()}");
                }
            }
        });

        return $updated;
    }

    /**
     * Get progress breakdown for debugging/display
     */
    public function getProgressBreakdown(Project $project): array
    {
        $tasks = $project->tasks()->get();

        $completed = $tasks->filter(fn($t) => $this->isTaskCompleted($t));
        $inProgress = $tasks->where('status', 'in-progress');
        $notStarted = $tasks->whereIn('status', ['pending', 'backlog', 'todo']);

        $totalEstimatedHours = $tasks->sum('estimate_hours') ?? 0;
        $completedEstimatedHours = $completed->sum('estimate_hours') ?? 0;

        return [
            'total' => $tasks->count(),
            'completed' => $completed->count(),
            'inProgress' => $inProgress->count(),
            'notStarted' => $notStarted->count(),
            'completionPercentage' => $this->calculateProgress($project, false),
            'totalEstimatedHours' => $totalEstimatedHours,
            'completedEstimatedHours' => $completedEstimatedHours,
            'remainingEstimatedHours' => max(0, $totalEstimatedHours - $completedEstimatedHours),
            'breakdown' => [
                'byStatus' => $tasks->groupBy('status')->map->count()->toArray(),
                'byPriority' => $tasks->groupBy('priority')->map->count()->toArray(),
            ],
            'milestones' => [
                'total' => $project->milestones()->count(),
                'completed' => $project->milestones()->where('status', 'completed')->count(),
            ],
        ];
    }

    /**
     * Calculate estimated completion date based on velocity
     */
    public function estimateCompletionDate(Project $project): ?\Carbon\Carbon
    {
        $breakdown = $this->getProgressBreakdown($project);

        if ($breakdown['completionPercentage'] >= 100) {
            return now(); // Already complete
        }

        if ($breakdown['remainingEstimatedHours'] <= 0) {
            return null; // No estimates available
        }

        // Calculate average hours completed per day (last 30 days)
        $completedTasksLast30Days = $project->tasks()
            ->whereIn('status', ['completed', 'done'])
            ->where('updated_at', '>=', now()->subDays(30))
            ->sum('estimate_hours');

        if ($completedTasksLast30Days <= 0) {
            return null; // No recent velocity data
        }

        $hoursPerDay = $completedTasksLast30Days / 30;
        $daysRemaining = $breakdown['remainingEstimatedHours'] / $hoursPerDay;

        return now()->addDays((int) ceil($daysRemaining));
    }

    /**
     * Clear cached progress for a project
     */
    public function clearCache(Project $project): void
    {
        Cache::forget("project_progress_{$project->id}");
    }
}
