<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAgileDefinitionRequest;
use App\Http\Requests\UpdateAgileDefinitionRequest;
use App\Models\AgileDefinition;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class AgileDefinitionController extends Controller
{
    public function index(): Response
    {
        $definitions = AgileDefinition::query()
            ->orderBy('sort_order')
            ->get()
            ->map(fn (AgileDefinition $definition) => $definition->toInertia())
            ->values();

        return Inertia::render('Agile/Definitions', [
            'dorItems' => $definitions->where('kind', 'ready')->values(),
            'dodItems' => $definitions->where('kind', 'done')->values(),
            'projects' => $this->projectOptions(),
        ]);
    }

    public function create(): Response
    {
        return $this->index()->with('formMode', 'create');
    }

    public function store(StoreAgileDefinitionRequest $request): RedirectResponse
    {
        AgileDefinition::query()->create($request->safe()->only([
            'project_id',
            'kind',
            'body',
            'is_checked',
            'sort_order',
        ]));

        return redirect()->route('agile.definitions')->with('success', 'Definition item added.');
    }

    public function show(AgileDefinition $agileDefinition): Response
    {
        return Inertia::render('Agile/Definitions', [
            'dorItems' => $agileDefinition->kind === 'ready' ? [$agileDefinition->toInertia()] : [],
            'dodItems' => $agileDefinition->kind === 'done' ? [$agileDefinition->toInertia()] : [],
            'definition' => $agileDefinition->toInertia(),
            'projects' => $this->projectOptions(),
        ]);
    }

    public function edit(AgileDefinition $agileDefinition): Response
    {
        return $this->show($agileDefinition)->with('formMode', 'edit');
    }

    public function update(UpdateAgileDefinitionRequest $request, AgileDefinition $agileDefinition): RedirectResponse
    {
        $agileDefinition->update($request->safe()->only([
            'project_id',
            'kind',
            'body',
            'is_checked',
            'sort_order',
        ]));

        return redirect()->route('agile.definitions')->with('success', 'Definition item updated.');
    }

    public function destroy(AgileDefinition $agileDefinition): RedirectResponse
    {
        $agileDefinition->delete();

        return redirect()->route('agile.definitions')->with('success', 'Definition item removed.');
    }
}
