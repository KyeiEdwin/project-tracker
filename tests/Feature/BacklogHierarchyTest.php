<?php

namespace Tests\Feature;

use App\Models\BacklogItem;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BacklogHierarchyTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->project = Project::factory()->create();
    }

    public function test_can_create_epic_without_parent(): void
    {
        $epic = BacklogItem::factory()->create([
            'project_id' => $this->project->id,
            'type' => 'epic',
            'parent_id' => null,
        ]);

        $this->assertNull($epic->parent_id);
        $this->assertEquals('epic', $epic->type);
    }

    public function test_can_create_feature_under_epic(): void
    {
        $epic = BacklogItem::factory()->create([
            'project_id' => $this->project->id,
            'type' => 'epic',
        ]);

        $feature = BacklogItem::factory()->create([
            'project_id' => $this->project->id,
            'type' => 'feature',
            'parent_id' => $epic->id,
        ]);

        $this->assertEquals($epic->id, $feature->parent_id);
        $this->assertTrue($epic->children->contains($feature));
    }

    public function test_can_create_story_under_feature(): void
    {
        $epic = BacklogItem::factory()->create([
            'project_id' => $this->project->id,
            'type' => 'epic',
        ]);

        $feature = BacklogItem::factory()->create([
            'project_id' => $this->project->id,
            'type' => 'feature',
            'parent_id' => $epic->id,
        ]);

        $story = BacklogItem::factory()->create([
            'project_id' => $this->project->id,
            'type' => 'story',
            'parent_id' => $feature->id,
        ]);

        $this->assertEquals($feature->id, $story->parent_id);
        $this->assertTrue($feature->children->contains($story));
    }

    public function test_cannot_set_item_as_its_own_parent(): void
    {
        $item = BacklogItem::factory()->create([
            'project_id' => $this->project->id,
            'type' => 'story',
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Cannot set item as its own parent');

        $item->parent_id = $item->id;
        $item->save();
    }

    public function test_cannot_create_circular_reference(): void
    {
        $itemA = BacklogItem::factory()->create([
            'project_id' => $this->project->id,
            'type' => 'feature',
        ]);

        $itemB = BacklogItem::factory()->create([
            'project_id' => $this->project->id,
            'type' => 'story',
            'parent_id' => $itemA->id,
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Circular parent reference');

        // Try to make A a child of B (circular)
        $itemA->parent_id = $itemB->id;
        $itemA->save();
    }

    public function test_cannot_set_parent_from_different_project(): void
    {
        $project2 = Project::factory()->create();

        $parentInProject1 = BacklogItem::factory()->create([
            'project_id' => $this->project->id,
            'type' => 'epic',
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Parent must belong to the same project');

        BacklogItem::factory()->create([
            'project_id' => $project2->id,
            'type' => 'story',
            'parent_id' => $parentInProject1->id,
        ]);
    }

    public function test_ancestors_method_returns_correct_hierarchy(): void
    {
        $epic = BacklogItem::factory()->create([
            'project_id' => $this->project->id,
            'type' => 'epic',
        ]);

        $feature = BacklogItem::factory()->create([
            'project_id' => $this->project->id,
            'type' => 'feature',
            'parent_id' => $epic->id,
        ]);

        $story = BacklogItem::factory()->create([
            'project_id' => $this->project->id,
            'type' => 'story',
            'parent_id' => $feature->id,
        ]);

        $ancestors = $story->ancestors();

        $this->assertCount(2, $ancestors);
        $this->assertTrue($ancestors->contains($feature));
        $this->assertTrue($ancestors->contains($epic));
    }

    public function test_descendants_method_returns_correct_hierarchy(): void
    {
        $epic = BacklogItem::factory()->create([
            'project_id' => $this->project->id,
            'type' => 'epic',
        ]);

        $feature1 = BacklogItem::factory()->create([
            'project_id' => $this->project->id,
            'type' => 'feature',
            'parent_id' => $epic->id,
        ]);

        $feature2 = BacklogItem::factory()->create([
            'project_id' => $this->project->id,
            'type' => 'feature',
            'parent_id' => $epic->id,
        ]);

        $story1 = BacklogItem::factory()->create([
            'project_id' => $this->project->id,
            'type' => 'story',
            'parent_id' => $feature1->id,
        ]);

        $story2 = BacklogItem::factory()->create([
            'project_id' => $this->project->id,
            'type' => 'story',
            'parent_id' => $feature1->id,
        ]);

        $descendants = $epic->descendants();

        $this->assertCount(4, $descendants);
        $this->assertTrue($descendants->contains($feature1));
        $this->assertTrue($descendants->contains($feature2));
        $this->assertTrue($descendants->contains($story1));
        $this->assertTrue($descendants->contains($story2));
    }

    public function test_roots_scope_returns_only_top_level_items(): void
    {
        $epic1 = BacklogItem::factory()->create([
            'project_id' => $this->project->id,
            'type' => 'epic',
            'parent_id' => null,
        ]);

        $epic2 = BacklogItem::factory()->create([
            'project_id' => $this->project->id,
            'type' => 'epic',
            'parent_id' => null,
        ]);

        $feature = BacklogItem::factory()->create([
            'project_id' => $this->project->id,
            'type' => 'feature',
            'parent_id' => $epic1->id,
        ]);

        $roots = BacklogItem::roots()->get();

        $this->assertCount(2, $roots);
        $this->assertTrue($roots->contains($epic1));
        $this->assertTrue($roots->contains($epic2));
        $this->assertFalse($roots->contains($feature));
    }

    public function test_within_parent_scope_filters_correctly(): void
    {
        $epic = BacklogItem::factory()->create([
            'project_id' => $this->project->id,
            'type' => 'epic',
        ]);

        $feature1 = BacklogItem::factory()->create([
            'project_id' => $this->project->id,
            'type' => 'feature',
            'parent_id' => $epic->id,
        ]);

        $feature2 = BacklogItem::factory()->create([
            'project_id' => $this->project->id,
            'type' => 'feature',
            'parent_id' => $epic->id,
        ]);

        $otherFeature = BacklogItem::factory()->create([
            'project_id' => $this->project->id,
            'type' => 'feature',
            'parent_id' => null,
        ]);

        $childrenOfEpic = BacklogItem::withinParent($epic->id)->get();

        $this->assertCount(2, $childrenOfEpic);
        $this->assertTrue($childrenOfEpic->contains($feature1));
        $this->assertTrue($childrenOfEpic->contains($feature2));
        $this->assertFalse($childrenOfEpic->contains($otherFeature));
    }

    public function test_deleting_parent_nullifies_children_parent_id(): void
    {
        $epic = BacklogItem::factory()->create([
            'project_id' => $this->project->id,
            'type' => 'epic',
        ]);

        $feature = BacklogItem::factory()->create([
            'project_id' => $this->project->id,
            'type' => 'feature',
            'parent_id' => $epic->id,
        ]);

        $epic->delete();

        $this->assertNull($feature->fresh()->parent_id);
    }
}
