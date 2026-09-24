<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\TeamMember;
use App\Models\User;

class ProjectPolicy
{
    public function view(User|TeamMember $actor, Project $project): bool
    {
        if ($actor instanceof TeamMember) {
            return $actor->hasPermission('member.project.view')
                && $actor->projects()->whereKey($project->id)->exists();
        }

        return $actor->hasPermission('project.view')
            && ($actor->role?->name === 'admin'
                || (int) $project->owner_id === (int) $actor->id
                || $actor->projects()->whereKey($project->id)->exists());
    }

    public function update(User|TeamMember $actor, Project $project): bool
    {
        return $actor instanceof TeamMember
            ? false
            : $actor->hasPermission('project.update')
                && ($actor->role?->name === 'admin' || (int) $project->owner_id === (int) $actor->id);
    }

    public function delete(User $user, Project $project): bool
    {
        return $user->hasPermission('project.delete')
            && ($user->role?->name === 'admin' || (int) $project->owner_id === (int) $user->id);
    }
}