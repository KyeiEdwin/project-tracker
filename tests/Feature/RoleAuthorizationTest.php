<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\TeamMember;
use App\Models\User;
use App\Policies\ProjectPolicy;
use App\Policies\TaskPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_manage_users(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin, 'web')
            ->get(route('team-members.index'))
            ->assertOk();
    }

    public function test_team_member_cannot_manage_users(): void
    {
        $member = TeamMember::factory()->create();

        $response = $this->actingAs($member, 'team_member')
            ->get(route('team-members.index'));

        $response->assertRedirect(route('login'));
        $this->assertDatabaseCount('team_members', 1);
    }

    public function test_team_member_can_view_only_assigned_projects(): void
    {
        $member = TeamMember::factory()->create();
        $assignedProject = Project::factory()->create();
        $unassignedProject = Project::factory()->create();
        $assignedProject->teamMembers()->attach($member);
        $policy = new ProjectPolicy();

        $this->assertTrue($policy->view($member, $assignedProject));
        $this->assertFalse($policy->view($member, $unassignedProject));
    }

    public function test_team_member_can_view_only_directly_assigned_tasks(): void
    {
        $member = TeamMember::factory()->create();
        $otherMember = TeamMember::factory()->create();
        $project = Project::factory()->create();
        $project->teamMembers()->attach([$member->id, $otherMember->id]);
        $assignedTask = Task::query()->create([
            'project_id' => $project->id,
            'team_member_id' => $member->id,
            'title' => 'Assigned task',
            'status' => 'pending',
            'priority' => 'medium',
        ]);
        $otherTask = Task::query()->create([
            'project_id' => $project->id,
            'team_member_id' => $otherMember->id,
            'title' => 'Other member task',
            'status' => 'pending',
            'priority' => 'medium',
        ]);
        $policy = new TaskPolicy();

        $this->assertTrue($policy->updateStatusForTeamMember($member, $assignedTask));
        $this->assertFalse($policy->updateStatusForTeamMember($member, $otherTask));
    }

    public function test_team_member_cannot_edit_project_data(): void
    {
        $member = TeamMember::factory()->create();
        $project = Project::factory()->create();
        $project->teamMembers()->attach($member);

        $this->assertFalse((new ProjectPolicy())->update($member, $project));

        $response = $this->actingAs($member, 'team_member')
            ->get(route('projects.edit', $project));

        $response->assertRedirect(route('login'));
    }

    public function test_team_member_cannot_delete_tasks(): void
    {
        $member = TeamMember::factory()->create();
        $project = Project::factory()->create();
        $project->teamMembers()->attach($member);
        $task = Task::query()->create([
            'project_id' => $project->id,
            'team_member_id' => $member->id,
            'title' => 'Protected task',
            'status' => 'pending',
            'priority' => 'medium',
        ]);

        $this->actingAs($member, 'team_member')
            ->delete(route('tasks.destroy', $task))
            ->assertRedirect(route('login'));

        $this->assertDatabaseHas('tasks', ['id' => $task->id]);
    }

    public function test_inactive_team_member_cannot_authenticate(): void
    {
        $member = TeamMember::factory()->create([
            'email' => 'inactive@example.test',
            'password' => 'password',
            'status' => 'inactive',
        ]);

        $this->post(route('login.store'), [
            'email' => $member->email,
            'password' => 'password',
        ])->assertRedirect();

        $this->assertGuest('team_member');

        $user = User::factory()->create([
            'email' => 'inactive-user@example.test',
            'password' => 'password',
            'is_active' => false,
        ]);

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect();

        $this->assertGuest('web');
    }

    public function test_removing_a_project_assignment_immediately_removes_member_access(): void
    {
        $member = TeamMember::factory()->create();
        $project = Project::factory()->create();
        $project->teamMembers()->attach($member);
        $task = Task::query()->create([
            'project_id' => $project->id,
            'team_member_id' => $member->id,
            'title' => 'Revoked task',
            'status' => 'pending',
            'priority' => 'medium',
        ]);

        $project->teamMembers()->detach($member);

        $this->assertFalse((new ProjectPolicy())->view($member, $project));

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

    private function makeAdmin(): User
    {
        $role = Role::query()->create([
            'name' => 'admin',
            'label' => 'Administrator',
        ]);
        $permissions = collect(config('authorization.permissions', []))->map(fn (string $name) => Permission::query()->updateOrCreate(
            ['name' => $name],
            ['label' => str($name)->replace('.', ' ')->title()]
        ));
        $role->permissions()->sync($permissions->pluck('id'));

        return User::factory()->create([
            'role_id' => $role->id,
            'email_verified_at' => now(),
            'is_active' => true,
        ]);
    }
}
