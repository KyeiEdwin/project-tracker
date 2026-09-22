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
        $page = $this->inertiaPage(
            Stakeholder::query()->with('project')->latest(),
            fn (Stakeholder $stakeholder) => $stakeholder->toInertia()
        );

        return Inertia::render('Initiation/Stakeholders', [
            'stakeholders' => $page['data'],
            'pagination' => $page['pagination'],
            'projects' => $this->projectOptions(),
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
