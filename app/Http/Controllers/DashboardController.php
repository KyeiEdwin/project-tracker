<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Dashboard', [
            'metrics' => [
                'totalProjects' => Project::query()->count(),
                'activeTasks' => 142,
                'teamMembers' => 18,
                'completionRate' => 87,
            ],
        ]);
    }
}
