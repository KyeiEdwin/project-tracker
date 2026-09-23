<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStakeholderRequest;
use App\Http\Requests\UpdateStakeholderRequest;
use App\Models\Stakeholder;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class StakeholderController extends Controller
{
    public function index(): Response
    {
        $projectId = request()->query('project_id');
        
        $query = Stakeholder::query()->with('project')->latest();
        
        if ($projectId) {
            $query->where('project_id', $projectId);
        }
        
        $page = $this->inertiaPage(
            $query,
            fn (Stakeholder $stakeholder) => $stakeholder->toInertia()
        );

        $currentProject = $projectId ? \App\Models\Project::find($projectId) : null;

        return Inertia::render('Initiation/Stakeholders', [
            'stakeholders' => $page['data'],
            'pagination' => $page['pagination'],
            'projects' => $this->projectOptions(),
            'currentProject' => $currentProject?->toInertia(),
            'filters' => ['project_id' => $projectId],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Initiation/Stakeholders', [
            'stakeholders' => [],
            'projects' => $this->projectOptions(),
            'formMode' => 'create',
        ]);
    }

    public function store(StoreStakeholderRequest $request): RedirectResponse
    {
        Stakeholder::query()->create($request->validated());

        return redirect()
            ->route('initiation.stakeholders')
            ->with('success', 'Stakeholder added.');
    }

    public function show(Stakeholder $stakeholder): Response
    {
        $stakeholder->load('project');

        return Inertia::render('Initiation/Stakeholders', [
            'stakeholders' => [$stakeholder->toInertia()],
            'stakeholder' => $stakeholder->toInertia(),
            'projects' => $this->projectOptions(),
        ]);
    }

    public function edit(Stakeholder $stakeholder): Response
    {
        $stakeholder->load('project');

        return Inertia::render('Initiation/Stakeholders', [
            'stakeholders' => [$stakeholder->toInertia()],
            'stakeholder' => $stakeholder->toInertia(),
            'projects' => $this->projectOptions(),
            'formMode' => 'edit',
        ]);
    }

    public function update(UpdateStakeholderRequest $request, Stakeholder $stakeholder): RedirectResponse
    {
        $stakeholder->update($request->validated());

        return redirect()
            ->route('initiation.stakeholders')
            ->with('success', 'Stakeholder updated.');
    }

    public function destroy(Stakeholder $stakeholder): RedirectResponse
    {
        $stakeholder->delete();

        return redirect()
            ->route('initiation.stakeholders')
            ->with('success', 'Stakeholder removed.');
    }
}
