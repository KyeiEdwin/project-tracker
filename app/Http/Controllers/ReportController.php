<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    public function analytics(): Response
    {
        return Inertia::render('Reports/Analytics');
    }

    public function documents(): Response
    {
        return Inertia::render('Reports/Documents');
    }

    public function lessonsLearned(): Response
    {
        return Inertia::render('Reports/LessonsLearned');
    }
}
