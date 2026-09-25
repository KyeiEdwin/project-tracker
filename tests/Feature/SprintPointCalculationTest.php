<?php

namespace Tests\Feature;

use App\Models\BacklogItem;
use App\Models\Project;
use App\Models\Sprint;
use App\Models\User;
use App\Services\SprintService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SprintPointCalculationTest extends TestCase
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

    public function test_adding_backlog_item_updates_planned_points(): void
    {
        $sprint = Sprint::factory()->create([
            'project_id' => $this->project->id,
            'status' => 'planned',
        ]);

        $this->assertEquals(0, $sprint->planned_points);

        BacklogItem::factory()->create([
            'project_id' => $this->project->id,
            'sprint_id' => $sprint->id,
            'points' => 5,
        ]);

        $this->assertEquals(5, $sprint->fresh()->planned_points);

        BacklogItem::factory()->create([
            'project_id' => $this->project->id,
            'sprint_id' => $sprint->id,
            'points' => 8,
        ]);

        $this->assertEquals(13, $sprint->fresh()->planned_points);
    }

    public function test_removing_item_from_sprint_updates_planned_points(): void
    {
        $sprint = Sprint::factory()->create([
            'project_id' => $this->project->id,
            'status' => 'planned',
        ]);

        $item = BacklogItem::factory()->create([
            'project_id' => $this->project->id,
            'sprint_id' => $sprint->id,
            'points' => 5,
        ]);

        $this->assertEquals(5, $sprint->fresh()->planned_points);

        $item->delete();

        $this->assertEquals(0, $sprint->fresh()->planned_points);
    }

    public function test_changing_item_points_updates_sprint_planned_points(): void
    {
        $sprint = Sprint::factory()->create([
            'project_id' => $this->project->id,
            'status' => 'planned',
        ]);

        $item = BacklogItem::factory()->create([
            'project_id' => $this->project->id,
            'sprint_id' => $sprint->id,
            'points' => 5,
        ]);

        $this->assertEquals(5, $sprint->fresh()->planned_points);

        $item->update(['points' => 8]);

        $this->assertEquals(8, $sprint->fresh()->planned_points);
    }

    public function test_marking_item_as_done_updates_completed_points(): void
    {
        $sprint = Sprint::factory()->create([
            'project_id' => $this->project->id,
            'status' => 'active',
        ]);

        $item = BacklogItem::factory()->create([
            'project_id' => $this->project->id,
            'sprint_id' => $sprint->id,
            'points' => 5,
            'status' => 'in-progress',
        ]);

        $this->assertEquals(0, $sprint->fresh()->completed_points);

        $item->update(['status' => 'done']);

        $this->assertEquals(5, $sprint->fresh()->completed_points);
    }

    public function test_moving_item_between_sprints_updates_both_sprints(): void
    {
        $sprint1 = Sprint::factory()->create([
            'project_id' => $this->project->id,
            'status' => 'planned',
        ]);

        $sprint2 = Sprint::factory()->create([
            'project_id' => $this->project->id,
            'status' => 'planned',
        ]);

        $item = BacklogItem::factory()->create([
            'project_id' => $this->project->id,
            'sprint_id' => $sprint1->id,
            'points' => 5,
        ]);

        $this->assertEquals(5, $sprint1->fresh()->planned_points);
        $this->assertEquals(0, $sprint2->fresh()->planned_points);

        $item->update(['sprint_id' => $sprint2->id]);

        $this->assertEquals(0, $sprint1->fresh()->planned_points);
        $this->assertEquals(5, $sprint2->fresh()->planned_points);
    }

    public function test_cannot_assign_item_from_different_project_to_sprint(): void
    {
        $project2 = Project::factory()->create();

        $sprint = Sprint::factory()->create([
            'project_id' => $this->project->id,
            'status' => 'planned',
        ]);

        $item = BacklogItem::factory()->create([
            'project_id' => $project2->id,
            'points' => 5,
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('different project');

        $this->sprintService->addBacklogItems($sprint, [$item->id]);
    }

    public function test_multiple_items_calculate_correctly(): void
    {
        $sprint = Sprint::factory()->create([
            'project_id' => $this->project->id,
            'status' => 'active',
        ]);

        BacklogItem::factory()->create([
            'project_id' => $this->project->id,
            'sprint_id' => $sprint->id,
            'points' => 3,
            'status' => 'done',
        ]);

        BacklogItem::factory()->create([
            'project_id' => $this->project->id,
            'sprint_id' => $sprint->id,
            'points' => 5,
            'status' => 'done',
        ]);

        BacklogItem::factory()->create([
            'project_id' => $this->project->id,
            'sprint_id' => $sprint->id,
            'points' => 8,
            'status' => 'in-progress',
        ]);

        BacklogItem::factory()->create([
            'project_id' => $this->project->id,
            'sprint_id' => $sprint->id,
            'points' => 13,
            'status' => 'backlog',
        ]);

        $sprint = $sprint->fresh();

        $this->assertEquals(29, $sprint->planned_points); // 3 + 5 + 8 + 13
        $this->assertEquals(8, $sprint->completed_points); // 3 + 5
    }

    public function test_sprint_events_are_logged_for_item_changes(): void
    {
        $sprint = Sprint::factory()->create([
            'project_id' => $this->project->id,
            'status' => 'active',
        ]);

        $item = BacklogItem::factory()->create([
            'project_id' => $this->project->id,
            'sprint_id' => $sprint->id,
            'points' => 5,
        ]);

        // Verify item_added event
        $this->assertDatabaseHas('sprint_events', [
            'sprint_id' => $sprint->id,
            'event_type' => 'item_added',
        ]);

        // Change points
        $item->update(['points' => 8]);

        // Verify estimate_changed event
        $this->assertDatabaseHas('sprint_events', [
            'sprint_id' => $sprint->id,
            'event_type' => 'estimate_changed',
        ]);

        // Change status
        $item->update(['status' => 'done']);

        // Verify status_updated event
        $this->assertDatabaseHas('sprint_events', [
            'sprint_id' => $sprint->id,
            'event_type' => 'status_updated',
        ]);

        // Remove from sprint
        $item->update(['sprint_id' => null]);

        // Verify item_removed event
        $this->assertDatabaseHas('sprint_events', [
            'sprint_id' => $sprint->id,
            'event_type' => 'item_removed',
        ]);
    }

    public function test_sprint_with_no_items_has_zero_points(): void
    {
        $sprint = Sprint::factory()->create([
            'project_id' => $this->project->id,
            'status' => 'planned',
        ]);

        $this->assertEquals(0, $sprint->planned_points);
        $this->assertEquals(0, $sprint->completed_points);
    }

    public function test_calculate_planned_points_service_method(): void
    {
        $sprint = Sprint::factory()->create([
            'project_id' => $this->project->id,
        ]);

        BacklogItem::factory()->count(3)->create([
            'project_id' => $this->project->id,
            'sprint_id' => $sprint->id,
            'points' => 5,
        ]);

        $calculated = $this->sprintService->calculatePlannedPoints($sprint);

        $this->assertEquals(15, $calculated);
    }

    public function test_calculate_completed_points_service_method(): void
    {
        $sprint = Sprint::factory()->create([
            'project_id' => $this->project->id,
        ]);

        BacklogItem::factory()->count(2)->create([
            'project_id' => $this->project->id,
            'sprint_id' => $sprint->id,
            'points' => 5,
            'status' => 'done',
        ]);

        BacklogItem::factory()->count(2)->create([
            'project_id' => $this->project->id,
            'sprint_id' => $sprint->id,
            'points' => 5,
            'status' => 'in-progress',
        ]);

        $calculated = $this->sprintService->calculateCompletedPoints($sprint);

        $this->assertEquals(10, $calculated);
    }
}
