<?php

namespace Tests\Feature;

use App\Events\ProjectProgressUpdated;
use App\Events\TaskUpdated;
use App\Models\Project;
use App\Models\Task;
use App\Services\ProjectProgressService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ProjectProgressServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_uses_completed_task_count_when_tasks_have_no_estimates(): void
    {
        $project = Project::factory()->create(['progress' => 0]);
        Task::query()->create($this->taskData($project, 'completed'));
        Task::query()->create($this->taskData($project, 'in-progress'));
        Task::query()->create($this->taskData($project, 'pending'));

        $progress = app(ProjectProgressService::class)->recalculate($project);

        $this->assertSame(33, $progress);
        $this->assertSame(33, $project->refresh()->progress);
    }

    public function test_it_uses_weighted_task_progress_when_estimates_exist(): void
    {
        $project = Project::factory()->create(['progress' => 0]);
        Task::query()->create($this->taskData($project, 'in-progress', 10, 50));
        Task::query()->create($this->taskData($project, 'pending', 30, 0));

        $progress = app(ProjectProgressService::class)->recalculate($project);

        $this->assertSame(13, $progress);
        $this->assertSame(13, $project->refresh()->progress);
    }

    public function test_completed_status_counts_as_full_progress_for_weighted_tasks(): void
    {
        $project = Project::factory()->create(['progress' => 0]);
        Task::query()->create($this->taskData($project, 'completed', 8, 0));
        Task::query()->create($this->taskData($project, 'pending', 8, 0));

        $progress = app(ProjectProgressService::class)->recalculate($project);

        $this->assertSame(50, $progress);
    }

    public function test_it_resets_progress_when_a_project_has_no_active_tasks(): void
    {
        $project = Project::factory()->create(['progress' => 75]);

        $progress = app(ProjectProgressService::class)->recalculate($project);

        $this->assertSame(0, $progress);
        $this->assertSame(0, $project->refresh()->progress);
    }

    public function test_task_updates_recalculate_progress_and_dispatch_project_events(): void
    {
        Event::fake();

        $project = Project::factory()->create(['progress' => 0]);
        $task = Task::query()->create($this->taskData($project, 'pending'));

        $response = $this->put(route('tasks.update', $task), [
            ...$this->taskData($project, 'completed'),
        ]);

        $response->assertRedirect(route('tasks.index'));
        $this->assertSame(100, $project->refresh()->progress);
        Event::assertDispatched(TaskUpdated::class, fn (TaskUpdated $event): bool =>
            $event->projectId === $project->id
                && $event->task['id'] === $task->id
                && $event->progress === 100
        );
        Event::assertDispatched(ProjectProgressUpdated::class, fn (ProjectProgressUpdated $event): bool =>
            $event->projectId === $project->id && $event->progress === 100
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function taskData(Project $project, string $status, float $estimateHours = 0, int $progress = 0): array
    {
        return [
            'project_id' => $project->id,
            'title' => ucfirst($status).' task',
            'status' => $status,
            'priority' => 'medium',
            'estimate_hours' => $estimateHours,
            'progress' => $progress,
        ];
    }
}