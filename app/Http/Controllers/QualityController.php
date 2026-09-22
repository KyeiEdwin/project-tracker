<?php

namespace App\Http\Controllers;

use Inertia\Response;

class QualityController extends Controller
{
    public function qaTesting(): Response
    {
        return app(QaTestController::class)->index();
    }

    public function risks(): Response
    {
        return app(RiskController::class)->index();
    }

    public function changeLog(): Response
    {
        return app(ChangeLogController::class)->index();
    }
}
