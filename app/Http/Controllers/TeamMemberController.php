<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTeamMemberRequest;
use App\Http\Requests\UpdateTeamMemberRequest;
use App\Models\TeamMember;
use App\Models\Team;
use App\Models\AuditLog;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class TeamMemberController extends Controller
{
    public function index(): Response
    {
        $projectId = request()->query('project_id');
        
        $query = TeamMember::query()->with(['projects', 'team'])->latest();
        
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
            'teams' => $this->teamOptions(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Resources/Team', [
            'teamMembers' => [],
            'projects' => $this->projectOptions(),
            'teams' => $this->teamOptions(),
            'formMode' => 'create',
        ]);
    }

    public function store(StoreTeamMemberRequest $request): RedirectResponse
    {
        $memberData = $request->safe()->only([
            'name',
            'email',
            'role',
            'department',
            'availability',
            'hourly_rate',
            'status',
        ]) + [
            'team_id' => $request->integer('team_id') ?: null,
            'role_id' => Role::query()->where('name', 'team_member')->value('id'),
        ];
        $memberData['password'] = Hash::make($request->input('password'));
        $member = TeamMember::query()->create($memberData);

        if ($request->filled('project_ids')) {
            $member->projects()->sync($request->input('project_ids'));
        }

        $this->audit($member, 'team_member.created', null, $member->toArray());

        return redirect()
            ->route('resources.team')
            ->with('success', 'Team member added.');
    }

    public function addToTeam(StoreTeamMemberRequest $request, \App\Models\Team $team): RedirectResponse
    {
        $member = TeamMember::query()->create($request->safe()->only([
            'name', 'email', 'role', 'department', 'availability', 'hourly_rate', 'status',
        ]) + [
            'team_id' => $team->id,
            'role_id' => Role::query()->where('name', 'team_member')->value('id'),
            'password' => Hash::make($request->input('password')),
        ]);

        if ($request->filled('project_ids')) {
            $member->projects()->sync($request->input('project_ids'));
        }

        $this->audit($member, 'team_member.created', null, $member->toArray());

        return back()->with('success', 'Team member added to the team.');
    }

    public function show(TeamMember $teamMember): Response
    {
        $teamMember->load(['projects', 'team']);

        return Inertia::render('Resources/Team', [
            'teamMembers' => [$teamMember->toInertia()],
            'teamMember' => $teamMember->toInertia(),
            'projects' => $this->projectOptions(),
            'teams' => $this->teamOptions(),
        ]);
    }

    public function edit(TeamMember $teamMember): Response
    {
        $teamMember->load(['projects', 'team']);

        return Inertia::render('Resources/Team', [
            'teamMembers' => [$teamMember->toInertia()],
            'teamMember' => $teamMember->toInertia(),
            'projects' => $this->projectOptions(),
            'teams' => $this->teamOptions(),
            'formMode' => 'edit',
        ]);
    }

    public function update(UpdateTeamMemberRequest $request, TeamMember $teamMember): RedirectResponse
    {
        $memberData = $request->safe()->only([
            'name',
            'email',
            'role',
            'department',
            'availability',
            'hourly_rate',
            'status',
            'team_id',
        ]);
        if ($request->filled('password')) {
            $memberData['password'] = Hash::make($request->input('password'));
        }
        $teamMember->update($memberData);

        if ($request->has('project_ids')) {
            $teamMember->projects()->sync($request->input('project_ids', []));
        }

        $this->audit($teamMember, 'team_member.updated', null, $memberData);

        return redirect()
            ->route('resources.team')
            ->with('success', 'Team member updated.');
    }

    public function destroy(TeamMember $teamMember): RedirectResponse
    {
        $this->audit($teamMember, 'team_member.deleted', $teamMember->toArray(), null);
        $teamMember->delete();

        return redirect()
            ->route('resources.team')
            ->with('success', 'Team member removed.');
    }

    /**
     * Return the selectable teams and their current member counts.
     * Keeping this on every team-member response also supports direct create/edit routes.
     *
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    private function teamOptions()
    {
        return Team::query()
            ->withCount('members')
            ->orderBy('name')
            ->get()
            ->map(fn (Team $team): array => [
                'id' => $team->id,
                'name' => $team->name,
                'slug' => $team->slug,
                'description' => $team->description,
                'status' => $team->status,
                'membersCount' => $team->members_count,
            ])
            ->values();
    }

    private function audit(TeamMember $member, string $action, ?array $oldValues, ?array $newValues): void
    {
        $actor = request()->user();

        AuditLog::query()->create([
            'actor_type' => $actor ? $actor::class : null,
            'actor_id' => $actor?->id,
            'action' => $action,
            'auditable_type' => $member::class,
            'auditable_id' => $member->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
