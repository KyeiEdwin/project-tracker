<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class ResourceController extends Controller
{
    public function team(): Response
    {
        return Inertia::render('Resources/Team');
    }

    public function timeTracking(): Response
    {
        return Inertia::render('Resources/TimeTracking');
    }

    public function budget(): Response
    {
        return Inertia::render('Resources/Budget');
    }

    public function milestones(): Response
    {
        return Inertia::render('Resources/Milestones');
    }

    public function gantt(): Response
    {
        return Inertia::render('Resources/Gantt');
    }
}
