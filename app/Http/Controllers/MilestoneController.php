<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMilestoneRequest;
use App\Http\Requests\UpdateMilestoneRequest;
use App\Models\Milestone;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class MilestoneController extends Controller
{
    public function index(): Response
    {
        $page = $this->inertiaPage(
            Milestone::query()->with('project')->orderBy('due_date'),
            fn (Milestone $milestone) => $milestone->toInertia()
        );

        return Inertia::render('Resources/Milestones', [
            'milestones' => $page['data'],
            'pagination' => $page['pagination'],
            'projects' => $this->projectOptions(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Resources/Milestones', [
            'milestones' => [],
            'projects' => $this->projectOptions(),
            'formMode' => 'create',
        ]);
    }

    public function store(StoreMilestoneRequest $request): RedirectResponse
    {
        $data = $request->validated();
        if (($data['status'] ?? null) === 'completed') {
            $data['completed_at'] = now();
        }

        Milestone::query()->create($data);

        return redirect()->route('resources.milestones')->with('success', 'Milestone added.');
    }

    public function show(Milestone $milestone): Response
    {
        $milestone->load('project');

        return Inertia::render('Resources/Milestones', [
            'milestones' => [$milestone->toInertia()],
            'milestone' => $milestone->toInertia(),
            'projects' => $this->projectOptions(),
        ]);
    }

    public function edit(Milestone $milestone): Response
    {
        $milestone->load('project');

        return Inertia::render('Resources/Milestones', [
            'milestones' => [$milestone->toInertia()],
            'milestone' => $milestone->toInertia(),
            'projects' => $this->projectOptions(),
            'formMode' => 'edit',
        ]);
    }

    public function update(UpdateMilestoneRequest $request, Milestone $milestone): RedirectResponse
    {
        $data = $request->validated();
        $data['completed_at'] = ($data['status'] ?? $milestone->status) === 'completed' ? now() : null;
        $milestone->update($data);

        return redirect()->route('resources.milestones')->with('success', 'Milestone updated.');
    }

    public function destroy(Milestone $milestone): RedirectResponse
    {
        $milestone->delete();

        return redirect()->route('resources.milestones')->with('success', 'Milestone removed.');
    }
}
