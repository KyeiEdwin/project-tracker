<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRiskRequest;
use App\Http\Requests\UpdateRiskRequest;
use App\Models\Risk;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class RiskController extends Controller
{
    public function index(): Response
    {
        $page = $this->inertiaPage(
            Risk::query()->with('project')->latest(),
            fn (Risk $risk) => $risk->toInertia()
        );

        return Inertia::render('Quality/Risks', [
            'risks' => $page['data'],
            'pagination' => $page['pagination'],
            'projects' => $this->projectOptions(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Quality/Risks', [
            'risks' => [],
            'projects' => $this->projectOptions(),
            'formMode' => 'create',
        ]);
    }

    public function store(StoreRiskRequest $request): RedirectResponse
    {
        Risk::query()->create($request->validated());

        return redirect()->route('quality.risks')->with('success', 'Risk added.');
    }

    public function show(Risk $risk): Response
    {
        $risk->load('project');

        return Inertia::render('Quality/Risks', [
            'risks' => [$risk->toInertia()],
            'risk' => $risk->toInertia(),
            'projects' => $this->projectOptions(),
        ]);
    }

    public function edit(Risk $risk): Response
    {
        $risk->load('project');

        return Inertia::render('Quality/Risks', [
            'risks' => [$risk->toInertia()],
            'risk' => $risk->toInertia(),
            'projects' => $this->projectOptions(),
            'formMode' => 'edit',
        ]);
    }

    public function update(UpdateRiskRequest $request, Risk $risk): RedirectResponse
    {
        $risk->update($request->validated());

        return redirect()->route('quality.risks')->with('success', 'Risk updated.');
    }

    public function destroy(Risk $risk): RedirectResponse
    {
        $risk->delete();

        return redirect()->route('quality.risks')->with('success', 'Risk removed.');
    }
}
