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
        $totalProjects = Project::query()->count();
        $completed = Project::query()->where('status', 'completed')->count();

        return Inertia::render('Dashboard', [
            'metrics' => [
                'totalProjects' => $totalProjects,
                'activeTasks' => Task::query()->whereNotIn('status', ['completed', 'done'])->count(),
                'teamMembers' => TeamMember::query()->count(),
                'completionRate' => $totalProjects > 0 ? (int) round(($completed / $totalProjects) * 100) : 0,
                'openRisks' => Risk::query()->where('status', '!=', 'closed')->count(),
            ],
        ]);
    }
}
