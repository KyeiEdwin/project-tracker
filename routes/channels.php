<?php

use App\Models\Project;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('project.{project}', function (User|TeamMember $actor, Project $project): bool {
    if ($actor instanceof TeamMember) {
        return $actor->projects()->whereKey($project->id)->exists();
    }

    return (int) $project->owner_id === (int) $actor->id;
}, ['guards' => ['web', 'team_member']]);

Broadcast::channel('team.{team}', function (TeamMember $actor, Team $team): bool {
    return $actor->status === 'active'
        && (int) $actor->team_id === (int) $team->id;
}, ['guards' => ['team_member']]);

Broadcast::channel('team-presence.{team}', function (TeamMember $actor, Team $team): ?array {
    if ($actor->status !== 'active' || (int) $actor->team_id !== (int) $team->id) {
        return null;
    }

    return [
        'id' => $actor->id,
        'name' => $actor->name,
        'role' => $actor->role,
    ];
}, ['guards' => ['team_member']]);