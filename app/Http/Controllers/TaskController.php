<?php

namespace App\Http\Controllers;

use App\Events\ProjectProgressUpdated;
use App\Events\TaskUpdated;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\Models\TaskDependency;
use App\Services\ProjectProgressService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Broadcasting\BroadcastException;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class TaskController extends Controller
{
    public function index(): Response
    {
        $page = $this->inertiaPage(
            Task::query()->whereIn('project_id', $this->accessibleProjectsQuery()->select('projects.id'))->with(['project', 'assignee'])->latest(),
            fn (Task $task) => $task->toInertia()
        );

        return Inertia::render('Tasks/Index', [
            'tasks' => $page['data'],
            'pagination' => $page['pagination'],
            'projects' => $this->projectOptions(),
            'teamMembers' => $this->teamMemberOptions(),
        ]);
    }

    public function kanban(): Response
    {
        $projectId = request()->integer('project_id') ?: null;
        $tasks = Task::query()
            ->whereIn('project_id', $this->accessibleProjectsQuery()->select('projects.id'))
            ->with(['project', 'assignee'])
            ->when($projectId, fn ($query) => $query->where('project_id', $projectId))
            ->orderBy('kanban_order')
            ->get();

        $groups = [
            'backlog' => ['id' => 'backlog', 'title' => 'Backlog', 'color' => 'secondary', 'statuses' => ['backlog', 'pending']],
            'todo' => ['id' => 'todo', 'title' => 'To Do', 'color' => 'warning', 'statuses' => ['todo']],
            'in-progress' => ['id' => 'in-progress', 'title' => 'In Progress', 'color' => 'primary', 'statuses' => ['in-progress']],
            'done' => ['id' => 'done', 'title' => 'Done', 'color' => 'success', 'statuses' => ['done', 'completed']],
        ];

        $columns = [];
        foreach ($groups as $group) {
            $columns[] = [
                'id' => $group['id'],
                'title' => $group['title'],
                'color' => $group['color'],
                'tasks' => $tasks
                    ->filter(fn (Task $task) => in_array($task->status, $group['statuses'], true))
                    ->map(fn (Task $task) => $task->toInertia())
                    ->values()
                    ->all(),
            ];
        }

        return Inertia::render('Tasks/Kanban', [
            'columns' => $columns,
            'projects' => $this->projectOptions(),
            'projectId' => $projectId,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Tasks/Index', [
            'tasks' => [],
            'projects' => $this->projectOptions(),
            'teamMembers' => $this->teamMemberOptions(),
            'formMode' => 'create',
        ]);
    }

    public function store(StoreTaskRequest $request, ProjectProgressService $progressService): RedirectResponse
    {
        abort_unless($this->accessibleProjectsQuery()->whereKey($request->integer('project_id'))->exists(), 404);

        $task = Task::query()->create($request->safe()->except('dependencies'));
        $this->syncAssigneeProjectMembership($task);
        $this->syncDependencies($task, $request->input('dependencies', []));
        $progress = $progressService->calculateProgress($task->project, update: true);
        $this->broadcastTaskChange($task, $progress);

        return redirect()->route('tasks.index')->with('success', 'Task created.');
    }

    public function show(Task $task): Response
    {
        $this->authorize('view', $task);

        $task->load(['project', 'assignee', 'subtasks.assignee', 'dependencies']);

        return Inertia::render('Tasks/Index', [
            'tasks' => [$task->toInertia()],
            'task' => $task->toInertia(),
            'projects' => $this->projectOptions(),
            'teamMembers' => $this->teamMemberOptions(),
        ]);
    }

    public function edit(Task $task): Response
    {
        $this->authorize('update', $task);

        $task->load(['project', 'assignee', 'subtasks']);

        return Inertia::render('Tasks/Index', [
            'tasks' => [$task->toInertia()],
            'task' => $task->toInertia(),
            'projects' => $this->projectOptions(),
            'teamMembers' => $this->teamMemberOptions(),
            'formMode' => 'edit',
        ]);
    }

    public function update(UpdateTaskRequest $request, Task $task, ProjectProgressService $progressService): RedirectResponse
    {
        $this->authorize('update', $task);

        $task->update($request->safe()->except('dependencies'));
        $this->syncAssigneeProjectMembership($task);

        if ($request->has('dependencies')) {
            $task->dependencies()->delete();
            $this->syncDependencies($task, $request->input('dependencies', []));
        }

        $progress = $progressService->calculateProgress($task->project, update: true);
        $this->broadcastTaskChange($task, $progress);

        return redirect()->route('tasks.index')->with('success', 'Task updated.');
    }

    private function syncAssigneeProjectMembership(Task $task): void
    {
        if ($task->team_member_id) {
            $task->project->teamMembers()->syncWithoutDetaching([$task->team_member_id]);
        }
    }

    public function destroy(Task $task, ProjectProgressService $progressService): RedirectResponse
    {
        $this->authorize('delete', $task);

        $project = $task->project;
        $task->subtasks()->delete();
        $task->dependencies()->delete();
        $task->dependents()->delete();
        $task->delete();
        $progress = $progressService->calculateProgress($project, update: true);
        $this->broadcastTaskChange($task, $progress, true);

        return redirect()->route('tasks.index')->with('success', 'Task removed.');
    }

    /**
     * @param  array<int, int>  $dependsOnIds
     */
    private function syncDependencies(Task $task, array $dependsOnIds): void
    {
        foreach ($dependsOnIds as $dependsOnId) {
            if ((int) $dependsOnId === $task->id) {
                continue;
            }

            TaskDependency::query()->create([
                'task_id' => $task->id,
                'depends_on_task_id' => $dependsOnId,
                'type' => 'blocks',
            ]);
        }
    }

    private function broadcastTaskChange(Task $task, int $progress, bool $deleted = false): void
    {
        $taskPayload = $task->toInertia();

        $events = [
            new TaskUpdated($task->project_id, $taskPayload, $progress, $deleted),
            new ProjectProgressUpdated($task->project_id, $progress),
        ];

        foreach ($events as $event) {
            try {
                event($event);
            } catch (BroadcastException $exception) {
                // Realtime is optional; a down WebSocket server must not roll back task changes.
                Log::warning('Realtime task broadcast skipped.', [
                    'event' => $event::class,
                    'project_id' => $task->project_id,
                    'error' => $exception->getMessage(),
                ]);
            }
        }
    }
}
