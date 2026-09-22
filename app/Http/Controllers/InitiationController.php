<?php

namespace App\Http\Controllers;

use Inertia\Response;

class InitiationController extends Controller
{
    public function kickoff(): Response
    {
        return app(KickoffController::class)->index();
    }

    public function stakeholders(): Response
    {
        return app(StakeholderController::class)->index();
    }
}
