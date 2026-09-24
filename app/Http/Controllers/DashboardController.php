<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Risk;
use App\Models\Task;
use App\Models\TeamMember;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        abort_unless(request()->user('web')?->hasPermission('dashboard.admin.view'), 403);

        $projects = $this->accessibleProjectsQuery();
        $totalProjects = (clone $projects)->count();
        $completed = (clone $projects)->where('status', 'completed')->count();

        return Inertia::render('Dashboard.Enhanced', [
            'metrics' => [
                'totalProjects' => $totalProjects,
                'activeTasks' => Task::query()->whereIn('project_id', $projects->select('projects.id'))->whereNotIn('status', ['completed', 'done'])->count(),
                'teamMembers' => TeamMember::query()->count(),
                'completionRate' => $totalProjects > 0 ? (int) round(($completed / $totalProjects) * 100) : 0,
                'openRisks' => Risk::query()->where('status', '!=', 'closed')->count(),
            ],
        ]);
    }
}
