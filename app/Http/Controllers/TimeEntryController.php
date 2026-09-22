<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTimeEntryRequest;
use App\Http\Requests\UpdateTimeEntryRequest;
use App\Models\TimeEntry;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class TimeEntryController extends Controller
{
    public function index(): Response
    {
        $page = $this->inertiaPage(
            TimeEntry::query()->with(['project', 'teamMember', 'task'])->latest('work_date'),
            fn (TimeEntry $entry) => $entry->toInertia()
        );

        $hours = TimeEntry::query();

        return Inertia::render('Resources/TimeTracking', [
            'timeEntries' => $page['data'],
            'pagination' => $page['pagination'],
            'weeklyTotal' => (float) (clone $hours)->whereBetween('work_date', [now()->startOfWeek(), now()->endOfWeek()])->sum('hours'),
            'monthlyTotal' => (float) (clone $hours)->whereBetween('work_date', [now()->startOfMonth(), now()->endOfMonth()])->sum('hours'),
            'projects' => $this->projectOptions(),
            'teamMembers' => $this->teamMemberOptions(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Resources/TimeTracking', [
            'timeEntries' => [],
            'weeklyTotal' => 0,
            'monthlyTotal' => 0,
            'projects' => $this->projectOptions(),
            'teamMembers' => $this->teamMemberOptions(),
            'formMode' => 'create',
        ]);
    }

    public function store(StoreTimeEntryRequest $request): RedirectResponse
    {
        TimeEntry::query()->create($request->safe()->only([
            'project_id',
            'team_member_id',
            'task_id',
            'work_date',
            'hours',
            'description',
            'is_billable',
        ]));

        return redirect()
            ->route('resources.time-tracking')
            ->with('success', 'Time entry logged.');
    }

    public function show(TimeEntry $timeEntry): Response
    {
        $timeEntry->load(['project', 'teamMember', 'task']);

        return Inertia::render('Resources/TimeTracking', [
            'timeEntries' => [$timeEntry->toInertia()],
            'timeEntry' => $timeEntry->toInertia(),
            'projects' => $this->projectOptions(),
            'teamMembers' => $this->teamMemberOptions(),
        ]);
    }

    public function edit(TimeEntry $timeEntry): Response
    {
        $timeEntry->load(['project', 'teamMember', 'task']);

        return Inertia::render('Resources/TimeTracking', [
            'timeEntries' => [$timeEntry->toInertia()],
            'timeEntry' => $timeEntry->toInertia(),
            'projects' => $this->projectOptions(),
            'teamMembers' => $this->teamMemberOptions(),
            'formMode' => 'edit',
        ]);
    }

    public function update(UpdateTimeEntryRequest $request, TimeEntry $timeEntry): RedirectResponse
    {
        $timeEntry->update($request->safe()->only([
            'project_id',
            'team_member_id',
            'task_id',
            'work_date',
            'hours',
            'description',
            'is_billable',
        ]));

        return redirect()
            ->route('resources.time-tracking')
            ->with('success', 'Time entry updated.');
    }

    public function destroy(TimeEntry $timeEntry): RedirectResponse
    {
        $timeEntry->delete();

        return redirect()
            ->route('resources.time-tracking')
            ->with('success', 'Time entry removed.');
    }
}
