<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\Models\TaskDependency;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class TaskController extends Controller
{
    public function index(): Response
    {
        $page = $this->inertiaPage(
            Task::query()->with(['project', 'assignee'])->latest(),
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
        $tasks = Task::query()->with(['project', 'assignee'])->orderBy('kanban_order')->get();

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

    public function store(StoreTaskRequest $request): RedirectResponse
    {
        $task = Task::query()->create($request->safe()->except('dependencies'));
        $this->syncDependencies($task, $request->input('dependencies', []));

        return redirect()->route('tasks.index')->with('success', 'Task created.');
    }

    public function show(Task $task): Response
    {
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
        $task->load(['project', 'assignee', 'subtasks']);

        return Inertia::render('Tasks/Index', [
            'tasks' => [$task->toInertia()],
            'task' => $task->toInertia(),
            'projects' => $this->projectOptions(),
            'teamMembers' => $this->teamMemberOptions(),
            'formMode' => 'edit',
        ]);
    }

    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
    {
        $task->update($request->safe()->except('dependencies'));

        if ($request->has('dependencies')) {
            $task->dependencies()->delete();
            $this->syncDependencies($task, $request->input('dependencies', []));
        }

        return redirect()->route('tasks.index')->with('success', 'Task updated.');
    }

    public function destroy(Task $task): RedirectResponse
    {
        $task->subtasks()->delete();
        $task->dependencies()->delete();
        $task->dependents()->delete();
        $task->delete();

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
}
