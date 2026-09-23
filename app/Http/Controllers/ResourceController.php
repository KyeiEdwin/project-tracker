<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Inertia\Inertia;
use Inertia\Response;

class ResourceController extends Controller
{
    public function team(): Response
    {
        return app(TeamMemberController::class)->index();
    }

    public function timeTracking(): Response
    {
        return app(TimeEntryController::class)->index();
    }

    public function budget(): Response
    {
        return app(BudgetItemController::class)->index();
    }

    public function milestones(): Response
    {
        return app(MilestoneController::class)->index();
    }

    public function gantt(): Response
    {
        $projectId = request()->query('project_id');
        
        $query = Task::query()->with('project')->orderBy('start_date')->orderBy('due_date');
        
        if ($projectId) {
            $query->where('project_id', $projectId);
        }
        
        $tasks = $query->get()->map(function (Task $task) {
            $payload = $task->toInertia();
            $start = $task->start_date ?? $task->project?->start_date;
            $payload['start'] = $start && $task->project?->start_date
                ? max(0, (int) $task->project->start_date->diffInWeeks($start, false))
                : 0;
            $payload['duration'] = $task->duration_days
                ? max(1, (int) ceil($task->duration_days / 7))
                : 1;
            $payload['color'] = $task->progress >= 100 ? 'bg-success' : ($task->progress > 0 ? 'bg-primary' : 'bg-secondary');

            return $payload;
        })->values();

        $currentProject = $projectId ? \App\Models\Project::find($projectId) : null;

        return Inertia::render('Resources/Gantt', [
            'tasks' => $tasks,
            'weeks' => collect(range(1, 8))->map(fn (int $week) => 'Week '.$week)->all(),
            'projects' => $this->projectOptions(),
            'currentProject' => $currentProject?->toInertia(),
            'filters' => ['project_id' => $projectId],
        ]);
    }
}
