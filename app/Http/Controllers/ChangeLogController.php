<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreChangeLogRequest;
use App\Http\Requests\UpdateChangeLogRequest;
use App\Models\ChangeLog;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ChangeLogController extends Controller
{
    public function index(): Response
    {
        $page = $this->inertiaPage(
            ChangeLog::query()->with('project')->latest('requested_on'),
            fn (ChangeLog $change) => $change->toInertia()
        );

        return Inertia::render('Quality/ChangeLog', [
            'changes' => $page['data'],
            'pagination' => $page['pagination'],
            'projects' => $this->projectOptions(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Quality/ChangeLog', [
            'changes' => [],
            'projects' => $this->projectOptions(),
            'formMode' => 'create',
        ]);
    }

    public function store(StoreChangeLogRequest $request): RedirectResponse
    {
        ChangeLog::query()->create($request->safe()->only([
            'project_id',
            'title',
            'type',
            'requestor',
            'status',
            'impact',
            'description',
            'requested_on',
        ]));

        return redirect()->route('quality.change-log')->with('success', 'Change request added.');
    }

    public function show(ChangeLog $changeLog): Response
    {
        $changeLog->load('project');

        return Inertia::render('Quality/ChangeLog', [
            'changes' => [$changeLog->toInertia()],
            'change' => $changeLog->toInertia(),
            'projects' => $this->projectOptions(),
        ]);
    }

    public function edit(ChangeLog $changeLog): Response
    {
        $changeLog->load('project');

        return Inertia::render('Quality/ChangeLog', [
            'changes' => [$changeLog->toInertia()],
            'change' => $changeLog->toInertia(),
            'projects' => $this->projectOptions(),
            'formMode' => 'edit',
        ]);
    }

    public function update(UpdateChangeLogRequest $request, ChangeLog $changeLog): RedirectResponse
    {
        $changeLog->update($request->safe()->only([
            'project_id',
            'title',
            'type',
            'requestor',
            'status',
            'impact',
            'description',
            'requested_on',
        ]));

        return redirect()->route('quality.change-log')->with('success', 'Change request updated.');
    }

    public function destroy(ChangeLog $changeLog): RedirectResponse
    {
        $changeLog->delete();

        return redirect()->route('quality.change-log')->with('success', 'Change request removed.');
    }
}
