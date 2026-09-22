<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
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

        $response = $this->post(route('tasks.store'), [
            'project_id' => $project->id,
            'title' => 'Deliver API integration',
            'description' => 'Created through the task endpoint.',
            'status' => 'todo',
            'priority' => 'high',
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
    }
}
