<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\TeamMember;
use App\Models\User;

class TaskPolicy
{
    public function view(User $user, Task $task): bool
    {
        return $user->hasPermission('task.view')
            && $this->userCanAccessProject($user, $task);
    }

    public function update(User $user, Task $task): bool
    {
        return $user->hasPermission('task.update')
            && $this->userCanAccessProject($user, $task);
    }

    public function delete(User $user, Task $task): bool
    {
        return $user->hasPermission('task.delete')
            && $this->userCanAccessProject($user, $task);
    }

    public function updateStatusForTeamMember(TeamMember $member, Task $task): bool
    {
        return $member->hasPermission('member.task.view')
            && $member->hasPermission('member.task.status.update')
            && (int) $task->team_member_id === (int) $member->id
            && $member->projects()->whereKey($task->project_id)->exists();
    }

    private function userCanAccessProject(User $user, Task $task): bool
    {
        return $user->role?->name === 'admin'
            || $task->project()->where('owner_id', $user->id)->exists()
            || $user->projects()->whereKey($task->project_id)->exists();
    }
}