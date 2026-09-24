<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\TeamMember;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamMemberTaskAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_team_member_uses_normalized_role_permissions(): void
    {
        $member = TeamMember::factory()->create();

        $this->assertNotNull($member->role_id);
        $this->assertSame('team_member', $member->memberRole->name);
        $this->assertTrue($member->hasPermission('member.dashboard.view'));
        $this->assertTrue($member->hasPermission('member.task.status.update'));
        $this->assertTrue($member->hasPermission('dashboard.team_member.view'));
        $this->assertFalse($member->hasPermission('user.manage'));
    }

    public function test_member_can_update_status_only_on_an_assigned_task(): void
    {
        $member = TeamMember::factory()->create();
        $project = Project::factory()->create();
        $project->teamMembers()->attach($member);
        $task = Task::query()->create([
            'project_id' => $project->id,
            'team_member_id' => $member->id,
            'title' => 'Assigned task',
            'status' => 'pending',
            'priority' => 'medium',
        ]);

        $this->actingAs($member, 'team_member')
            ->patch(route('team-member.tasks.status', $task), [
                'status' => 'completed',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'completed',
            'progress' => 100,
        ]);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'task.status_changed',
            'task_id' => $task->id,
            'actor_id' => $member->id,
        ]);
    }

    public function test_member_cannot_update_a_task_assigned_to_another_member(): void
    {
        $member = TeamMember::factory()->create();
        $otherMember = TeamMember::factory()->create();
        $project = Project::factory()->create();
        $project->teamMembers()->attach([$member->id, $otherMember->id]);
        $task = Task::query()->create([
            'project_id' => $project->id,
            'team_member_id' => $otherMember->id,
            'title' => 'Private task',
            'status' => 'pending',
            'priority' => 'medium',
        ]);

        $this->actingAs($member, 'team_member')
            ->patch(route('team-member.tasks.status', $task), [
                'status' => 'completed',
            ])
            ->assertNotFound();

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'pending',
        ]);
    }

    public function test_member_cannot_update_a_task_in_an_unassigned_project(): void
    {
        $member = TeamMember::factory()->create();
        $otherMember = TeamMember::factory()->create();
        $project = Project::factory()->create();
        $task = Task::query()->create([
            'project_id' => $project->id,
            'team_member_id' => $member->id,
            'title' => 'Unassigned project task',
            'status' => 'pending',
            'priority' => 'medium',
        ]);

        $this->actingAs($member, 'team_member')
            ->patch(route('team-member.tasks.status', $task), [
                'status' => 'completed',
            ])
            ->assertNotFound();

        $this->assertDatabaseMissing('audit_logs', [
            'task_id' => $task->id,
        ]);
    }

    public function test_dashboard_returns_only_the_members_assigned_projects_and_tasks(): void
    {
        $member = TeamMember::factory()->create();
        $otherMember = TeamMember::factory()->create();
        $assignedProject = Project::factory()->create();
        $unassignedProject = Project::factory()->create();
        $assignedProject->teamMembers()->attach($member);

        $visibleTask = Task::query()->create([
            'project_id' => $assignedProject->id,
            'team_member_id' => $member->id,
            'title' => 'Visible task',
            'status' => 'pending',
            'priority' => 'medium',
        ]);
        $otherTask = Task::query()->create([
            'project_id' => $assignedProject->id,
            'team_member_id' => $otherMember->id,
            'title' => 'Other member task',
            'status' => 'pending',
            'priority' => 'medium',
        ]);
        $unassignedTask = Task::query()->create([
            'project_id' => $unassignedProject->id,
            'team_member_id' => $member->id,
            'title' => 'Unassigned project task',
            'status' => 'pending',
            'priority' => 'medium',
        ]);

        $this->actingAs($member, 'team_member')
            ->get(route('team-member.dashboard'))
            ->assertInertia(fn ($page) => $page
                ->where('projects.0.id', $assignedProject->id)
                ->where('tasks.0.id', $visibleTask->id)
                ->missing('tasks.1')
                ->where('metrics.totalTasks', 1)
            );

        $this->assertNotSame($visibleTask->id, $otherTask->id);
        $this->assertNotSame($visibleTask->id, $unassignedTask->id);
    }

    public function test_dashboard_metrics_and_task_details_are_database_backed(): void
    {
        $member = TeamMember::factory()->create();
        $assignedProject = Project::factory()->create(['name' => 'Assigned project']);
        $secondAssignedProject = Project::factory()->create(['name' => 'Second project']);
        $unassignedProject = Project::factory()->create(['name' => 'Unassigned project']);

        $member->projects()->attach([$assignedProject->id, $secondAssignedProject->id]);

        Task::query()->create([
            'project_id' => $assignedProject->id,
            'team_member_id' => $member->id,
            'title' => 'Completed task',
            'status' => 'completed',
            'priority' => 'medium',
        ]);
        Task::query()->create([
            'project_id' => $assignedProject->id,
            'team_member_id' => $member->id,
            'title' => 'Done task',
            'status' => 'done',
            'priority' => 'medium',
        ]);
        Task::query()->create([
            'project_id' => $assignedProject->id,
            'team_member_id' => $member->id,
            'title' => 'In progress task',
            'status' => 'in-progress',
            'priority' => 'medium',
        ]);
        $detailedTask = Task::query()->create([
            'project_id' => $assignedProject->id,
            'team_member_id' => $member->id,
            'title' => 'Detailed task',
            'description' => 'Task description',
            'status' => 'pending',
            'priority' => 'high',
        ]);
        Task::query()->create([
            'project_id' => $unassignedProject->id,
            'team_member_id' => $member->id,
            'title' => 'Hidden task',
            'status' => 'completed',
            'priority' => 'medium',
        ]);

        $this->actingAs($member, 'team_member')
            ->get(route('team-member.dashboard'))
            ->assertInertia(fn ($page) => $page
                ->has('projects', 2)
                ->has('tasks', 4)
                ->where('metrics.projects', 2)
                ->where('metrics.totalTasks', 4)
                ->where('metrics.completedTasks', 2)
                ->where('metrics.inProgressTasks', 1)
                ->where('tasks', function ($tasks) use ($detailedTask, $member): bool {
                    $task = collect($tasks)->firstWhere('id', $detailedTask->id);

                    return $task !== null
                        && $task['title'] === 'Detailed task'
                        && $task['project'] === 'Assigned project'
                        && $task['status'] === 'pending'
                        && $task['assignee'] === $member->name;
                }));
    }

    public function test_member_cannot_open_admin_project_or_task_routes(): void
    {
        $member = TeamMember::factory()->create();

        $projectResponse = $this->actingAs($member, 'team_member')
            ->get('/projects/create');
        $projectResponse->assertStatus(302);
        $this->assertSame(route('login'), $projectResponse->headers->get('Location'));

        $taskResponse = $this->actingAs($member, 'team_member')
            ->get('/tasks');
        $taskResponse->assertStatus(302);
        $this->assertSame(route('login'), $taskResponse->headers->get('Location'));
    }

    public function test_team_member_cannot_open_the_admin_dashboard(): void
    {
        $member = TeamMember::factory()->create();

        $response = $this->actingAs($member, 'team_member')->get(route('dashboard'));

        $response->assertStatus(302);
        $this->assertSame(route('login'), $response->headers->get('Location'));
    }

    public function test_member_cannot_use_an_unscoped_task_id_for_status_updates(): void
    {
        $member = TeamMember::factory()->create();
        $project = Project::factory()->create();
        $task = Task::query()->create([
            'project_id' => $project->id,
            'team_member_id' => $member->id,
            'title' => 'Unassigned project task',
            'status' => 'pending',
            'priority' => 'medium',
        ]);

        $this->actingAs($member, 'team_member')
            ->patch(route('team-member.tasks.status', $task->id), [
                'status' => 'completed',
            ])
            ->assertNotFound();
    }
}