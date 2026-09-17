<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Dashboard', [
            'metrics' => [
                'totalProjects' => 24,
                'activeTasks' => 142,
                'teamMembers' => 18,
                'completionRate' => 87,
            ],
        ]);
    }
}
