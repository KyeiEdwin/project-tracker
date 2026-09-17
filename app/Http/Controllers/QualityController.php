<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class QualityController extends Controller
{
    public function qaTesting(): Response
    {
        return Inertia::render('Quality/QaTesting');
    }

    public function risks(): Response
    {
        return Inertia::render('Quality/Risks');
    }

    public function changeLog(): Response
    {
        return Inertia::render('Quality/ChangeLog');
    }
}
