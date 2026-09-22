<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubtaskRequest;
use App\Http\Requests\UpdateSubtaskRequest;
use App\Models\Subtask;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SubtaskController extends Controller
{
    public function index(): Response
    {
        $page = $this->inertiaPage(
            Subtask::query()->with(['task.project', 'assignee'])->latest(),
            fn (Subtask $subtask) => $subtask->toInertia()
        );

        return Inertia::render('Tasks/Index', [
            'tasks' => [],
            'subtasks' => $page['data'],
            'pagination' => $page['pagination'],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Tasks/Index', [
            'tasks' => [],
            'formMode' => 'create-subtask',
            'teamMembers' => $this->teamMemberOptions(),
        ]);
    }

    public function store(StoreSubtaskRequest $request): RedirectResponse
    {
        Subtask::query()->create($request->validated());

        return redirect()->route('tasks.index')->with('success', 'Subtask created.');
    }

    public function show(Subtask $subtask): Response
    {
        $subtask->load('assignee');

        return Inertia::render('Tasks/Index', [
            'tasks' => [],
            'subtask' => $subtask->toInertia(),
        ]);
    }

    public function edit(Subtask $subtask): Response
    {
        return $this->show($subtask)->with('formMode', 'edit-subtask');
    }

    public function update(UpdateSubtaskRequest $request, Subtask $subtask): RedirectResponse
    {
        $subtask->update($request->validated());

        return redirect()->route('tasks.index')->with('success', 'Subtask updated.');
    }

    public function destroy(Subtask $subtask): RedirectResponse
    {
        $subtask->delete();

        return redirect()->route('tasks.index')->with('success', 'Subtask removed.');
    }
}
