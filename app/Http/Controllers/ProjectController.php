<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function index(): Response
    {
        $page = $this->inertiaPage(
            $this->accessibleProjectsQuery()->with('teamRelation')->latest(),
            fn (Project $project) => $project->toInertia()
        );

        return Inertia::render('Projects/Index', [
            'projects' => $page['data'],
            'pagination' => $page['pagination'],
            'teams' => $this->teamOptions(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Projects/Create', ['teams' => $this->teamOptions()]);
    }

    public function store(StoreProjectRequest $request): RedirectResponse
    {
        $project = Project::query()->create($this->payload($request->validated()));

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Project created successfully.');
    }

    public function show(Project $project): Response
    {
        $this->authorize('view', $project);

        $project->load([
            'teamRelation',
            'tasks.assignee', 
            'milestones', 
            'risks', 
            'stakeholders',
            'teamMembers',
            'reports',
            'documents'
        ]);

        return Inertia::render('Projects/Show', [
            'project' => $project->toInertia(),
            'tasks' => $project->tasks->map->toInertia()->values(),
            'milestones' => $project->milestones->map->toInertia()->values(),
            'risks' => $project->risks->map->toInertia()->values(),
            'stakeholders' => $project->stakeholders->map->toInertia()->values(),
            'teamMembers' => $this->teamMemberOptions(),
            'stats' => [
                'totalTasks' => $project->tasks->count(),
                'completedTasks' => $project->tasks->where('status', 'completed')->count(),
                'openRisks' => $project->risks->where('status', '!=', 'closed')->count(),
                'stakeholdersCount' => $project->stakeholders->count(),
                'teamMembersCount' => $project->teamMembers->count(),
                'documentsCount' => $project->documents->count(),
            ],
            'teams' => $this->teamOptions(),
        ]);
    }

    public function dashboard(Project $project): Response
    {
        $this->authorize('view', $project);

        $project->load(['teamRelation', 'tasks', 'risks', 'sprints', 'users', 'teamMembers']);
        $tasks = $project->tasks;
        $openRisks = $project->risks->where('status', '!=', 'closed')->values();
        $sprint = $project->sprints
            ->whereIn('status', ['active', 'in-progress'])
            ->sortByDesc('start_date')
            ->first()
            ?? $project->sprints->sortByDesc('start_date')->first();
        return Inertia::render('Projects/MemberDashboard', [
            'project' => $project->toInertia(),
            'dashboard' => [
                'progress' => (int) $project->progress,
                'tasks' => [
                    'total' => $tasks->count(),
                    'completed' => $tasks->whereIn('status', ['completed', 'done'])->count(),
                    'inProgress' => $tasks->where('status', 'in-progress')->count(),
                ],
                'sprint' => $sprint?->toInertia(),
                'teamMembers' => $project->users->count() ?: $project->teamMembers->count(),
                'risks' => $openRisks->count(),
                'openIssues' => $openRisks->count(),
                'tasksList' => $tasks->map->toInertia()->values(),
            ],
        ]);
    }

    public function edit(Project $project): Response
    {
        $this->authorize('update', $project);

        return Inertia::render('Projects/Create', [
            'project' => $project->toInertia(),
            'formMode' => 'edit',
            'teams' => $this->teamOptions(),
        ]);
    }

    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        $this->authorize('update', $project);

        $project->update($this->payload($request->validated(), $project));

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $this->authorize('delete', $project);

        $project->delete();

        return redirect()
            ->route('projects.index')
            ->with('success', 'Project archived.');
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function payload(array $validated, ?Project $existing = null): array
    {
        $teamLabels = [
            'development' => 'Development Team',
            'marketing' => 'Marketing Team',
            'design' => 'Design Team',
            'qa' => 'QA Team',
        ];

        $settings = $existing?->settings ?? [];
        foreach (['phases', 'milestones', 'deliverables', 'sprint_duration', 'sprint_goal', 'velocity', 'methodology', 'sprint_length', 'phase_count'] as $key) {
            if (array_key_exists($key, $validated)) {
                $settings[$key] = $validated[$key];
            }
        }

        return [
            'name' => $validated['name'],
            'description' => $validated['description'] ?? $existing?->description,
            'priority' => $validated['priority'],
            'status' => $validated['status'],
            'start_date' => $validated['start_date'] ?? $existing?->start_date,
            'due_date' => $validated['end_date'] ?? $validated['due_date'] ?? $existing?->due_date,
            'progress' => $validated['progress'] ?? $existing?->progress ?? 0,
            'project_type' => $validated['project_type'] ?? $existing?->project_type,
            'team' => $teamLabels[$validated['team'] ?? ''] ?? ($validated['team'] ?? $existing?->team),
            'client' => $validated['client'] ?? $existing?->client,
            'settings' => $settings,
            'owner_id' => $existing?->owner_id ?? auth()->id(),
            'team_id' => $validated['team_id'] ?? $existing?->team_id,
        ];
    }

    /** @return array<int, array{id: int, name: string, status: string}> */
    private function teamOptions(): array
    {
        return Team::query()
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'status'])
            ->map(fn (Team $team): array => $team->toArray())
            ->all();
    }
}
