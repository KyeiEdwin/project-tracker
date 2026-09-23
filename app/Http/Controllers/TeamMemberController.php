<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTeamMemberRequest;
use App\Http\Requests\UpdateTeamMemberRequest;
use App\Models\TeamMember;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class TeamMemberController extends Controller
{
    public function index(): Response
    {
        $projectId = request()->query('project_id');
        
        $query = TeamMember::query()->with('projects')->latest();
        
        if ($projectId) {
            $query->whereHas('projects', function ($q) use ($projectId) {
                $q->where('projects.id', $projectId);
            });
        }
        
        $page = $this->inertiaPage(
            $query,
            fn (TeamMember $member) => $member->toInertia()
        );

        $currentProject = $projectId ? \App\Models\Project::find($projectId) : null;

        return Inertia::render('Resources/Team', [
            'teamMembers' => $page['data'],
            'pagination' => $page['pagination'],
            'projects' => $this->projectOptions(),
            'currentProject' => $currentProject?->toInertia(),
            'filters' => ['project_id' => $projectId],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Resources/Team', [
            'teamMembers' => [],
            'projects' => $this->projectOptions(),
            'formMode' => 'create',
        ]);
    }

    public function store(StoreTeamMemberRequest $request): RedirectResponse
    {
        $member = TeamMember::query()->create($request->safe()->only([
            'name',
            'email',
            'role',
            'department',
            'availability',
            'hourly_rate',
            'status',
        ]));

        if ($request->filled('project_ids')) {
            $member->projects()->sync($request->input('project_ids'));
        }

        return redirect()
            ->route('resources.team')
            ->with('success', 'Team member added.');
    }

    public function show(TeamMember $teamMember): Response
    {
        $teamMember->load('projects');

        return Inertia::render('Resources/Team', [
            'teamMembers' => [$teamMember->toInertia()],
            'teamMember' => $teamMember->toInertia(),
            'projects' => $this->projectOptions(),
        ]);
    }

    public function edit(TeamMember $teamMember): Response
    {
        $teamMember->load('projects');

        return Inertia::render('Resources/Team', [
            'teamMembers' => [$teamMember->toInertia()],
            'teamMember' => $teamMember->toInertia(),
            'projects' => $this->projectOptions(),
            'formMode' => 'edit',
        ]);
    }

    public function update(UpdateTeamMemberRequest $request, TeamMember $teamMember): RedirectResponse
    {
        $teamMember->update($request->safe()->only([
            'name',
            'email',
            'role',
            'department',
            'availability',
            'hourly_rate',
            'status',
        ]));

        if ($request->has('project_ids')) {
            $teamMember->projects()->sync($request->input('project_ids', []));
        }

        return redirect()
            ->route('resources.team')
            ->with('success', 'Team member updated.');
    }

    public function destroy(TeamMember $teamMember): RedirectResponse
    {
        $teamMember->delete();

        return redirect()
            ->route('resources.team')
            ->with('success', 'Team member removed.');
    }
}
