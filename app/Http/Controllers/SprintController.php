<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSprintRequest;
use App\Http\Requests\UpdateSprintRequest;
use App\Models\Sprint;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SprintController extends Controller
{
    public function index(): Response
    {
        $page = $this->inertiaPage(
            Sprint::query()->with('project')->latest('start_date'),
            fn (Sprint $sprint) => $sprint->toInertia()
        );

        $current = Sprint::query()
            ->with('project')
            ->where('status', 'active')
            ->latest('start_date')
            ->first();

        $taskCounts = ['total' => 0, 'done' => 0, 'inProgress' => 0, 'todo' => 0];

        if ($current) {
            $tasks = Task::query()->where('sprint_id', $current->id);
            $taskCounts['total'] = (clone $tasks)->count();
            $taskCounts['done'] = (clone $tasks)->whereIn('status', ['completed', 'done'])->count();
            $taskCounts['inProgress'] = (clone $tasks)->where('status', 'in-progress')->count();
            $taskCounts['todo'] = (clone $tasks)->whereIn('status', ['pending', 'todo', 'backlog'])->count();
        }

        return Inertia::render('Agile/Sprints', [
            'sprints' => $page['data'],
            'pagination' => $page['pagination'],
            'currentSprint' => $current ? array_merge($current->toInertia(), [
                'totalPoints' => (int) $current->planned_points,
                'completedPoints' => (int) $current->completed_points,
                'tasks' => $taskCounts,
            ]) : [
                'name' => 'No active sprint',
                'goal' => 'Create a sprint to start tracking velocity.',
                'daysRemaining' => 0,
                'totalPoints' => 0,
                'completedPoints' => 0,
                'tasks' => $taskCounts,
            ],
            'projects' => $this->projectOptions(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Agile/Sprints', [
            'sprints' => [],
            'currentSprint' => null,
            'projects' => $this->projectOptions(),
            'formMode' => 'create',
        ]);
    }

    public function store(StoreSprintRequest $request): RedirectResponse
    {
        Sprint::query()->create($request->validated());

        return redirect()->route('agile.sprints')->with('success', 'Sprint created.');
    }

    public function show(Sprint $sprint): Response
    {
        $sprint->load('project');

        return Inertia::render('Agile/Sprints', [
            'sprints' => [$sprint->toInertia()],
            'sprint' => $sprint->toInertia(),
            'currentSprint' => $sprint->toInertia(),
            'projects' => $this->projectOptions(),
        ]);
    }

    public function edit(Sprint $sprint): Response
    {
        $sprint->load('project');

        return Inertia::render('Agile/Sprints', [
            'sprints' => [$sprint->toInertia()],
            'sprint' => $sprint->toInertia(),
            'projects' => $this->projectOptions(),
            'formMode' => 'edit',
        ]);
    }

    public function update(UpdateSprintRequest $request, Sprint $sprint): RedirectResponse
    {
        $sprint->update($request->validated());

        return redirect()->route('agile.sprints')->with('success', 'Sprint updated.');
    }

    public function destroy(Sprint $sprint): RedirectResponse
    {
        $sprint->delete();

        return redirect()->route('agile.sprints')->with('success', 'Sprint removed.');
    }
}
