<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class AgileController extends Controller
{
    public function sprints(): Response
    {
        return Inertia::render('Agile/Sprints');
    }

    public function backlog(): Response
    {
        return Inertia::render('Agile/Backlog');
    }

    public function definitions(): Response
    {
        return Inertia::render('Agile/Definitions');
    }
}
