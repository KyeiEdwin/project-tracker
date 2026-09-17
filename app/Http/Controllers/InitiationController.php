<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class InitiationController extends Controller
{
    public function kickoff(): Response
    {
        return Inertia::render('Initiation/Kickoff');
    }

    public function stakeholders(): Response
    {
        return Inertia::render('Initiation/Stakeholders');
    }
}
