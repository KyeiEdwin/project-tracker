<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Task;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class TaskCreationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:fresh', ['--force' => true]);
    }

    public function test_task_creation_persists_the_task_and_its_dependency(): void
    {
        $this->actingAs($this->makeAdmin(), 'web');
        $project = Project::query()->create([
            'name' => 'API Test Project',
            'priority' => 'high',
            'status' => 'planning',
        ]);

        $dependency = Task::query()->create([
            'project_id' => $project->id,
            'title' => 'Prepare environment',
            'status' => 'pending',
            'priority' => 'medium',
        ]);
        $member = TeamMember::factory()->create();

        $response = $this->post(route('tasks.store'), [
            'project_id' => $project->id,
            'title' => 'Deliver API integration',
            'description' => 'Created through the task endpoint.',
            'status' => 'todo',
            'priority' => 'high',
            'team_member_id' => $member->id,
            'start_date' => '2026-09-22',
            'due_date' => '2026-09-30',
            'estimate_hours' => 12.5,
            'dependencies' => [$dependency->id],
        ]);

        $response->assertRedirect(route('tasks.index'));

        $task = Task::query()->where('title', 'Deliver API integration')->firstOrFail();

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'project_id' => $project->id,
            'status' => 'todo',
            'priority' => 'high',
        ]);
        $this->assertDatabaseHas('task_dependencies', [
            'task_id' => $task->id,
            'depends_on_task_id' => $dependency->id,
            'type' => 'blocks',
        ]);
        $this->assertDatabaseHas('project_team_member', [
            'project_id' => $project->id,
            'team_member_id' => $member->id,
        ]);
    }

    public function test_reassigning_a_task_allocates_the_new_member_to_the_project(): void
    {
        $this->actingAs($this->makeAdmin(), 'web');
        $project = Project::query()->create([
            'name' => 'Reassignment Project',
            'priority' => 'medium',
            'status' => 'planning',
        ]);
        $oldMember = TeamMember::factory()->create();
        $newMember = TeamMember::factory()->create();
        $task = Task::query()->create([
            'project_id' => $project->id,
            'team_member_id' => $oldMember->id,
            'title' => 'Reassigned task',
            'status' => 'pending',
            'priority' => 'medium',
        ]);
        $project->teamMembers()->attach($oldMember);

        $this->put(route('tasks.update', $task), [
            'project_id' => $project->id,
            'title' => 'Reassigned task',
            'status' => 'pending',
            'priority' => 'medium',
            'team_member_id' => $newMember->id,
        ])->assertRedirect(route('tasks.index'));

        $this->assertDatabaseHas('project_team_member', [
            'project_id' => $project->id,
            'team_member_id' => $oldMember->id,
        ]);
        $this->assertDatabaseHas('project_team_member', [
            'project_id' => $project->id,
            'team_member_id' => $newMember->id,
        ]);
    }

    private function makeAdmin(): User
    {
        $role = Role::query()->create(['name' => 'admin', 'label' => 'Administrator']);
        $permissions = collect(config('authorization.permissions', []))->map(fn (string $name) => Permission::query()->updateOrCreate(
            ['name' => $name],
            ['label' => str($name)->replace('.', ' ')->title()]
        ));
        $role->permissions()->sync($permissions->pluck('id'));

        return User::factory()->create([
            'role_id' => $role->id,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }
}
