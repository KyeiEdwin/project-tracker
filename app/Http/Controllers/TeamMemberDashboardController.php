<?php

namespace App\Http\Controllers;

use App\Models\TeamMember;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TeamMemberDashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        /** @var TeamMember $member */
        $member = $request->user('team_member');
        abort_unless($member->hasPermission('dashboard.team_member.view'), 403);
        $canViewProjects = $member->hasPermission('member.project.view');
        $canViewTasks = $member->hasPermission('member.task.view');
        $member->load([
            'team',
            'projects' => fn ($query) => $canViewProjects ? $query : $query->whereKey(0),
        ]);

        $tasks = $canViewTasks
            ? $member->assignedTasks()->with(['project', 'sprint', 'assignee'])->latest()->get()
            : collect();
        $roleContent = match ($member->role) {
            'manager' => [
                'title' => 'Team overview',
                'description' => 'Track delivery across your assigned projects and team.',
                'canManageTeam' => false,
            ],
            'designer' => [
                'title' => 'Design work',
                'description' => 'Review the tasks assigned to your design role.',
                'canManageTeam' => false,
            ],
            'tester' => [
                'title' => 'Quality queue',
                'description' => 'Focus on assigned testing and verification tasks.',
                'canManageTeam' => false,
            ],
            default => [
                'title' => 'My delivery dashboard',
                'description' => 'Work through your assigned project tasks.',
                'canManageTeam' => false,
            ],
        };

        return Inertia::render('TeamMembers/Dashboard', [
            'member' => $member->toInertia(),
            'role' => $member->role,
            'roleContent' => $roleContent,
            'team' => $member->team ? [
                'id' => $member->team->id,
                'name' => $member->team->name,
                'slug' => $member->team->slug,
                'description' => $member->team->description,
                'status' => $member->team->status,
            ] : null,
            'projects' => $member->projects->map->toInertia()->values(),
            'tasks' => $tasks->map->toInertia()->values(),
            'metrics' => [
                'totalTasks' => $tasks->count(),
                'completedTasks' => $tasks->whereIn('status', ['completed', 'done'])->count(),
                'inProgressTasks' => $tasks->where('status', 'in-progress')->count(),
                'projects' => $member->projects->count(),
            ],
        ]);
    }
}