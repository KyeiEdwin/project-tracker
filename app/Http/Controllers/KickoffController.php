<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKickoffRequest;
use App\Http\Requests\UpdateKickoffRequest;
use App\Models\Kickoff;
use App\Models\KickoffObjective;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class KickoffController extends Controller
{
    public function index(): Response
    {
        $page = $this->inertiaPage(
            Kickoff::query()->with('project')->latest('scheduled_on'),
            fn (Kickoff $kickoff) => $kickoff->toInertia()
        );

        $objectives = KickoffObjective::query()
            ->orderBy('sort_order')
            ->get()
            ->map(fn (KickoffObjective $objective) => $objective->toInertia())
            ->values();

        return Inertia::render('Initiation/Kickoff', [
            'kickoffs' => $page['data'],
            'pagination' => $page['pagination'],
            'objectives' => $objectives,
            'projects' => $this->projectOptions(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Initiation/Kickoff', [
            'kickoffs' => [],
            'objectives' => [],
            'projects' => $this->projectOptions(),
            'formMode' => 'create',
        ]);
    }

    public function store(StoreKickoffRequest $request): RedirectResponse
    {
        $kickoff = Kickoff::query()->create($request->safe()->only([
            'project_id',
            'scheduled_on',
            'location',
            'attendees_count',
            'agenda',
            'notes',
            'status',
        ]));

        $this->syncObjectives($kickoff, $request->input('objectives', []));

        return redirect()
            ->route('initiation.kickoff')
            ->with('success', 'Kick-off scheduled.');
    }

    public function show(Kickoff $kickoff): Response
    {
        $kickoff->load(['project', 'objectives']);

        return Inertia::render('Initiation/Kickoff', [
            'kickoffs' => [$kickoff->toInertia()],
            'objectives' => $kickoff->objectives->map->toInertia()->values(),
            'projects' => $this->projectOptions(),
            'kickoff' => $kickoff->toInertia(),
        ]);
    }

    public function edit(Kickoff $kickoff): Response
    {
        $kickoff->load(['project', 'objectives']);

        return Inertia::render('Initiation/Kickoff', [
            'kickoffs' => [$kickoff->toInertia()],
            'objectives' => $kickoff->objectives->map->toInertia()->values(),
            'projects' => $this->projectOptions(),
            'kickoff' => $kickoff->toInertia(),
            'formMode' => 'edit',
        ]);
    }

    public function update(UpdateKickoffRequest $request, Kickoff $kickoff): RedirectResponse
    {
        $kickoff->update($request->safe()->only([
            'project_id',
            'scheduled_on',
            'location',
            'attendees_count',
            'agenda',
            'notes',
            'status',
        ]));

        if ($request->has('objectives')) {
            $kickoff->objectives()->delete();
            $this->syncObjectives($kickoff, $request->input('objectives', []));
        }

        return redirect()
            ->route('initiation.kickoff')
            ->with('success', 'Kick-off updated.');
    }

    public function destroy(Kickoff $kickoff): RedirectResponse
    {
        $kickoff->objectives()->delete();
        $kickoff->delete();

        return redirect()
            ->route('initiation.kickoff')
            ->with('success', 'Kick-off removed.');
    }

    /**
     * @param  array<int, array<string, mixed>>  $objectives
     */
    private function syncObjectives(Kickoff $kickoff, array $objectives): void
    {
        foreach ($objectives as $index => $objective) {
            $kickoff->objectives()->create([
                'body' => $objective['text'] ?? $objective['body'] ?? '',
                'is_completed' => (bool) ($objective['completed'] ?? $objective['is_completed'] ?? false),
                'sort_order' => $index,
            ]);
        }
    }
}
