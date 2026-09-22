<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function index(): Response
    {
        $projects = Project::query()
            ->latest()
            ->get()
            ->map(fn (Project $project) => $project->toInertia())
            ->values();

        return Inertia::render('Projects/Index', [
            'projects' => $projects,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Projects/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|string|in:low,medium,high,critical',
            'status' => 'required|string|in:planning,in-progress,on-hold,completed',
            'dueDate' => 'nullable|date',
            'endDate' => 'nullable|date',
            'startDate' => 'nullable|date',
            'budget' => 'nullable|numeric',
            'projectType' => 'nullable|string|max:32',
            'team' => 'nullable|string|max:255',
            'client' => 'nullable|string|max:255',
            'phases' => 'nullable',
            'milestones' => 'nullable|string',
            'deliverables' => 'nullable|string',
            'sprintDuration' => 'nullable|string',
            'sprintGoal' => 'nullable|string',
            'velocity' => 'nullable',
            'methodology' => 'nullable|string',
            'sprintLength' => 'nullable|string',
            'phaseCount' => 'nullable',
        ]);

        $teamLabels = [
            'development' => 'Development Team',
            'marketing' => 'Marketing Team',
            'design' => 'Design Team',
            'qa' => 'QA Team',
        ];

        $project = Project::query()->create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'priority' => $validated['priority'],
            'status' => $validated['status'],
            'start_date' => $validated['startDate'] ?? null,
            'due_date' => $validated['endDate'] ?? $validated['dueDate'] ?? null,
            'budget' => $validated['budget'] ?? null,
            'project_type' => $validated['projectType'] ?? null,
            'team' => $teamLabels[$validated['team'] ?? ''] ?? ($validated['team'] ?? null),
            'client' => $validated['client'] ?? null,
            'settings' => [
                'phases' => $validated['phases'] ?? null,
                'milestones' => $validated['milestones'] ?? null,
                'deliverables' => $validated['deliverables'] ?? null,
                'sprintDuration' => $validated['sprintDuration'] ?? null,
                'sprintGoal' => $validated['sprintGoal'] ?? null,
                'velocity' => $validated['velocity'] ?? null,
                'methodology' => $validated['methodology'] ?? null,
                'sprintLength' => $validated['sprintLength'] ?? null,
                'phaseCount' => $validated['phaseCount'] ?? null,
            ],
        ]);

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Project created successfully.');
    }

    public function show(Project $project): Response
    {
        return Inertia::render('Projects/Show', [
            'project' => $project->toInertia(),
        ]);
    }
}
