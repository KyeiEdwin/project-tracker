<?php

use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('project.{project}', function (User $user, Project $project): bool {
    return (int) $project->owner_id === (int) $user->id;
});