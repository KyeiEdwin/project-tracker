<?php

namespace App\Http\Controllers;

use Inertia\Response;

class AgileController extends Controller
{
    public function sprints(): Response
    {
        return app(SprintController::class)->index();
    }

    public function backlog(): Response
    {
        return app(BacklogItemController::class)->index();
    }

    public function definitions(): Response
    {
        return app(AgileDefinitionController::class)->index();
    }
}
