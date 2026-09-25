<?php

namespace Tests\Feature;

use App\Models\BacklogItem;
use App\Models\Project;
use App\Models\Sprint;
use App\Models\User;
use App\Services\SprintService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SprintLifecycleTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Project $project;
    protected SprintService $sprintService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->project = Project::factory()->create();
        $this->sprintService = app(SprintService::class);
    }

    public function test_can_create_planned_sprint(): void
    {
        $sprint = Sprint::factory()->create([
            'project_id' => $this->project->id,
            'status' => 'planned',
            'start_date' => now()->addDays(1),
            'end_date' => now()->addDays(15),
        ]);

        $this->assertDatabaseHas('sprints', [
            'id' => $sprint->id,
            'status' => 'planned',
            'project_id' => $this->project->id,
        ]);
    }

    public function test_can_start_planned_sprint(): void
    {
        $sprint = Sprint::factory()->create([
            'project_id' => $this->project->id,
            'status' => 'planned',
            'start_date' => now(),
            'end_date' => now()->addDays(14),
        ]);

        // Add some backlog items
        BacklogItem::factory()->count(3)->create([
            'project_id' => $this->project->id,
            'sprint_id' => $sprint->id,
            'points' => 5,
        ]);

        $this->sprintService->startSprint($sprint);

        $this->assertEquals('active', $sprint->fresh()->status);
        
        // Verify event was logged
        $this->assertDatabaseHas('sprint_events', [
            'sprint_id' => $sprint->id,
            'event_type' => 'sprint_started',
        ]);

        // Verify snapshot was created
        $this->assertDatabaseHas('sprint_snapshots', [
            'sprint_id' => $sprint->id,
        ]);
    }

    public function test_cannot_start_two_active_sprints_in_same_project(): void
    {
        // Create and start first sprint
        $sprint1 = Sprint::factory()->create([
            'project_id' => $this->project->id,
            'status' => 'active',
        ]);

        // Try to create second active sprint
        $sprint2 = Sprint::factory()->create([
            'project_id' => $this->project->id,
            'status' => 'planned',
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Another sprint is already active');

        $this->sprintService->startSprint($sprint2);
    }

    public function test_starting_new_sprint_auto_closes_expired_sprint(): void
    {
        // Create expired active sprint
        $expiredSprint = Sprint::factory()->create([
            'project_id' => $this->project->id,
            'status' => 'active',
            'start_date' => now()->subDays(20),
            'end_date' => now()->subDays(6),
        ]);

        // Create new planned sprint
        $newSprint = Sprint::factory()->create([
            'project_id' => $this->project->id,
            'status' => 'planned',
            'start_date' => now(),
            'end_date' => now()->addDays(14),
        ]);

        $this->sprintService->startSprint($newSprint);

        // Verify expired sprint was auto-closed
        $this->assertEquals('completed', $expiredSprint->fresh()->status);
        $this->assertEquals('active', $newSprint->fresh()->status);
    }

    public function test_can_close_active_sprint(): void
    {
        $sprint = Sprint::factory()->create([
            'project_id' => $this->project->id,
            'status' => 'active',
        ]);

        BacklogItem::factory()->count(3)->create([
            'project_id' => $this->project->id,
            'sprint_id' => $sprint->id,
            'points' => 5,
            'status' => 'done',
        ]);

        $this->sprintService->closeSprint($sprint);

        $this->assertEquals('completed', $sprint->fresh()->status);
        
        // Verify event was logged
        $this->assertDatabaseHas('sprint_events', [
            'sprint_id' => $sprint->id,
            'event_type' => 'sprint_closed',
        ]);
    }

    public function test_cannot_close_already_completed_sprint(): void
    {
        $sprint = Sprint::factory()->create([
            'project_id' => $this->project->id,
            'status' => 'completed',
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Only active sprints can be closed');

        $this->sprintService->closeSprint($sprint);
    }

    public function test_cannot_modify_completed_sprint(): void
    {
        $sprint = Sprint::factory()->create([
            'project_id' => $this->project->id,
            'status' => 'completed',
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Cannot modify completed sprint');

        $sprint->name = 'Modified Name';
        $sprint->save();
    }

    public function test_cannot_delete_completed_sprint(): void
    {
        $sprint = Sprint::factory()->create([
            'project_id' => $this->project->id,
            'status' => 'completed',
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Cannot delete completed sprint');

        $sprint->delete();
    }

    public function test_sprint_lifecycle_workflow(): void
    {
        // 1. Create planned sprint
        $sprint = Sprint::factory()->create([
            'project_id' => $this->project->id,
            'status' => 'planned',
            'start_date' => now(),
            'end_date' => now()->addDays(14),
        ]);

        // 2. Add backlog items
        $items = BacklogItem::factory()->count(5)->create([
            'project_id' => $this->project->id,
            'sprint_id' => $sprint->id,
            'points' => 5,
            'status' => 'backlog',
        ]);

        // 3. Start sprint
        $this->sprintService->startSprint($sprint);
        $this->assertEquals('active', $sprint->fresh()->status);
        $this->assertEquals(25, $sprint->fresh()->planned_points);

        // 4. Complete some items
        $items->take(3)->each(function ($item) {
            $item->update(['status' => 'done']);
        });

        $this->assertEquals(15, $sprint->fresh()->completed_points);

        // 5. Close sprint
        $this->sprintService->closeSprint($sprint);
        $this->assertEquals('completed', $sprint->fresh()->status);

        // 6. Verify immutability
        $this->expectException(\RuntimeException::class);
        $sprint->fresh()->update(['name' => 'Should fail']);
    }
}
