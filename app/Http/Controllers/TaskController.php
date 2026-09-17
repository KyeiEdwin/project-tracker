<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class TaskController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Tasks/Index');
    }

    public function kanban(): Response
    {
        return Inertia::render('Tasks/Kanban');
    }

    public function workflows(): Response
    {
        return Inertia::render('Tasks/Workflows');
    }
}
