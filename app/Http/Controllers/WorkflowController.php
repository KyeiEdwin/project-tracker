<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWorkflowRequest;
use App\Http\Requests\UpdateWorkflowRequest;
use App\Models\Workflow;
use App\Models\WorkflowState;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class WorkflowController extends Controller
{
    public function index(): Response
    {
        $workflows = Workflow::query()
            ->with(['states', 'tasks'])
            ->latest()
            ->get()
            ->map(fn (Workflow $workflow) => $workflow->toInertia())
            ->values();

        return Inertia::render('Tasks/Workflows', [
            'workflows' => $workflows,
            'projects' => $this->projectOptions(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Tasks/Workflows', [
            'workflows' => [],
            'projects' => $this->projectOptions(),
            'formMode' => 'create',
        ]);
    }

    public function store(StoreWorkflowRequest $request): RedirectResponse
    {
        $workflow = Workflow::query()->create($request->safe()->only([
            'project_id',
            'name',
            'description',
            'is_default',
        ]));

        $this->syncStates($workflow, $request->input('states', []));

        return redirect()->route('tasks.workflows')->with('success', 'Workflow created.');
    }

    public function show(Workflow $workflow): Response
    {
        $workflow->load(['states', 'tasks']);

        return Inertia::render('Tasks/Workflows', [
            'workflows' => [$workflow->toInertia()],
            'workflow' => $workflow->toInertia(),
            'projects' => $this->projectOptions(),
        ]);
    }

    public function edit(Workflow $workflow): Response
    {
        $workflow->load('states');

        return Inertia::render('Tasks/Workflows', [
            'workflows' => [$workflow->toInertia()],
            'workflow' => $workflow->toInertia(),
            'projects' => $this->projectOptions(),
            'formMode' => 'edit',
        ]);
    }

    public function update(UpdateWorkflowRequest $request, Workflow $workflow): RedirectResponse
    {
        $workflow->update($request->safe()->only([
            'project_id',
            'name',
            'description',
            'is_default',
        ]));

        if ($request->has('states')) {
            $workflow->states()->delete();
            $this->syncStates($workflow, $request->input('states', []));
        }

        return redirect()->route('tasks.workflows')->with('success', 'Workflow updated.');
    }

    public function destroy(Workflow $workflow): RedirectResponse
    {
        $workflow->states()->delete();
        $workflow->delete();

        return redirect()->route('tasks.workflows')->with('success', 'Workflow removed.');
    }

    /**
     * @param  array<int, array<string, mixed>>  $states
     */
    private function syncStates(Workflow $workflow, array $states): void
    {
        foreach ($states as $index => $state) {
            $name = $state['name'] ?? '';

            WorkflowState::query()->create([
                'workflow_id' => $workflow->id,
                'name' => $name,
                'slug' => Str::slug($name).'-'.$index,
                'color' => $state['color'] ?? null,
                'sort_order' => $index,
                'is_initial' => (bool) ($state['is_initial'] ?? $index === 0),
                'is_final' => (bool) ($state['is_final'] ?? false),
            ]);
        }
    }
}
