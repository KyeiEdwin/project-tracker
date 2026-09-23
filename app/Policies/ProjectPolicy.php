<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function view(User $user, Project $project): bool
    {
        return (int) $project->owner_id === (int) $user->id;
    }

    public function update(User $user, Project $project): bool
    {
        return $this->view($user, $project);
    }
}