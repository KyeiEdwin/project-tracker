<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class ChatController extends Controller
{
    public function index(): Response
    {
        $projectId = request()->query('project_id');
        
        $currentProject = $projectId ? \App\Models\Project::find($projectId) : null;

        return Inertia::render('Communication/Chat', [
            'currentProject' => $currentProject?->toInertia(),
            'filters' => ['project_id' => $projectId],
            'projects' => $this->projectOptions(),
        ]);
    }
}
