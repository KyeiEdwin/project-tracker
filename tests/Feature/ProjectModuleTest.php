<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\Risk;
use App\Models\Stakeholder;
use App\Models\TeamMember;
use App\Models\Document;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectModuleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_loads_project_details_with_all_relationships()
    {
        // Arrange: Create a project with related data
        $project = Project::factory()->create();
        $tasks = Task::factory()->count(3)->create(['project_id' => $project->id]);
        $tasks[0]->update(['status' => 'completed']);
        $risks = Risk::factory()->count(2)->create(['project_id' => $project->id, 'status' => 'open']);
        $stakeholders = Stakeholder::factory()->count(4)->create(['project_id' => $project->id]);
        $teamMembers = TeamMember::factory()->count(3)->create();
        $project->teamMembers()->attach($teamMembers->pluck('id'));
        $documents = Document::factory()->count(2)->create(['project_id' => $project->id]);

        // Act: Visit project show page
        $response = $this->get(route('projects.show', $project));

        // Assert: Check response
        $response->assertOk();
        $response->assertInertia(fn ($page) => 
            $page->component('Projects/Show')
                ->has('project')
                ->has('tasks', 3)
                ->has('risks', 2)
                ->has('stakeholders', 4)
                ->has('teamMembers')
                ->has('stats', fn ($stats) =>
                    $stats->where('totalTasks', 3)
                        ->where('completedTasks', 1)
                        ->where('openRisks', 2)
                        ->where('stakeholdersCount', 4)
                        ->where('teamMembersCount', 3)
                        ->where('documentsCount', 2)
                )
        );
    }

    /** @test */
    public function it_calculates_stats_correctly()
    {
        // Arrange
        $project = Project::factory()->create();
        
        // Create 5 tasks, 2 completed
        Task::factory()->count(2)->create(['project_id' => $project->id, 'status' => 'completed']);
        Task::factory()->count(3)->create(['project_id' => $project->id, 'status' => 'in-progress']);
        
        // Create 3 risks, 1 closed
        Risk::factory()->count(2)->create(['project_id' => $project->id, 'status' => 'open']);
        Risk::factory()->create(['project_id' => $project->id, 'status' => 'closed']);
        
        // Create 4 stakeholders
        Stakeholder::factory()->count(4)->create(['project_id' => $project->id]);

        // Act
        $response = $this->get(route('projects.show', $project));

        // Assert stats are calculated correctly
        $response->assertInertia(fn ($page) =>
            $page->has('stats', fn ($stats) =>
                $stats->where('totalTasks', 5)
                    ->where('completedTasks', 2)
                    ->where('openRisks', 2) // Only non-closed risks
                    ->where('stakeholdersCount', 4)
            )
        );
    }

    /** @test */
    public function edit_button_navigates_to_edit_page()
    {
        // Arrange
        $project = Project::factory()->create();

        // Act
        $response = $this->get(route('projects.edit', $project));

        // Assert
        $response->assertOk();
        $response->assertInertia(fn ($page) =>
            $page->component('Projects/Create')
                ->has('project')
                ->where('formMode', 'edit')
        );
    }

    /** @test */
    public function stakeholders_can_be_filtered_by_project()
    {
        // Arrange
        $project1 = Project::factory()->create();
        $project2 = Project::factory()->create();
        
        Stakeholder::factory()->count(3)->create(['project_id' => $project1->id]);
        Stakeholder::factory()->count(2)->create(['project_id' => $project2->id]);

        // Act: Request stakeholders for project1
        $response = $this->get(route('initiation.stakeholders', ['project_id' => $project1->id]));

        // Assert: Only project1 stakeholders returned
        $response->assertOk();
        $response->assertInertia(fn ($page) =>
            $page->component('Initiation/Stakeholders')
                ->has('stakeholders', 3)
                ->has('currentProject')
                ->where('filters.project_id', $project1->id)
        );
    }

    /** @test */
    public function risks_can_be_filtered_by_project()
    {
        // Arrange
        $project1 = Project::factory()->create();
        $project2 = Project::factory()->create();
        
        Risk::factory()->count(4)->create(['project_id' => $project1->id]);
        Risk::factory()->count(2)->create(['project_id' => $project2->id]);

        // Act
        $response = $this->get(route('quality.risks', ['project_id' => $project1->id]));

        // Assert
        $response->assertOk();
        $response->assertInertia(fn ($page) =>
            $page->component('Quality/Risks')
                ->has('risks', 4)
                ->has('currentProject')
                ->where('filters.project_id', $project1->id)
        );
    }

    /** @test */
    public function team_members_can_be_filtered_by_project()
    {
        // Arrange
        $project1 = Project::factory()->create();
        $project2 = Project::factory()->create();
        
        $teamMembers1 = TeamMember::factory()->count(3)->create();
        $teamMembers2 = TeamMember::factory()->count(2)->create();
        
        $project1->teamMembers()->attach($teamMembers1->pluck('id'));
        $project2->teamMembers()->attach($teamMembers2->pluck('id'));

        // Act
        $response = $this->get(route('resources.team', ['project_id' => $project1->id]));

        // Assert
        $response->assertOk();
        $response->assertInertia(fn ($page) =>
            $page->component('Resources/Team')
                ->has('teamMembers', 3)
                ->has('currentProject')
        );
    }

    /** @test */
    public function gantt_chart_filters_tasks_by_project()
    {
        // Arrange
        $project1 = Project::factory()->create();
        $project2 = Project::factory()->create();
        
        Task::factory()->count(5)->create(['project_id' => $project1->id]);
        Task::factory()->count(3)->create(['project_id' => $project2->id]);

        // Act
        $response = $this->get(route('resources.gantt', ['project_id' => $project1->id]));

        // Assert
        $response->assertOk();
        $response->assertInertia(fn ($page) =>
            $page->component('Resources/Gantt')
                ->has('tasks', 5)
                ->has('currentProject')
        );
    }

    /** @test */
    public function reports_filter_by_project()
    {
        // Arrange
        $project1 = Project::factory()->create();
        $project2 = Project::factory()->create();
        
        // Create tasks for metrics
        Task::factory()->count(10)->create(['project_id' => $project1->id]);
        Task::factory()->count(5)->create(['project_id' => $project2->id]);

        // Act
        $response = $this->get(route('reports.analytics', ['project_id' => $project1->id]));

        // Assert
        $response->assertOk();
        $response->assertInertia(fn ($page) =>
            $page->component('Reports/Analytics')
                ->has('currentProject')
                ->has('metrics')
                ->where('filters.project_id', $project1->id)
        );
    }

    /** @test */
    public function project_crud_operations_work()
    {
        // Test Create
        $projectData = [
            'name' => 'Test Project',
            'description' => 'Test Description',
            'priority' => 'high',
            'status' => 'planning',
            'budget' => 50000,
            'team' => 'development',
        ];

        $response = $this->post(route('projects.store'), $projectData);
        $response->assertRedirect();
        $this->assertDatabaseHas('projects', ['name' => 'Test Project']);

        // Test Read
        $project = Project::where('name', 'Test Project')->first();
        $response = $this->get(route('projects.show', $project));
        $response->assertOk();

        // Test Update
        $response = $this->put(route('projects.update', $project), [
            'name' => 'Updated Project',
            'priority' => 'medium',
            'status' => 'in-progress',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('projects', ['name' => 'Updated Project']);

        // Test Delete
        $response = $this->delete(route('projects.destroy', $project));
        $response->assertRedirect();
        $this->assertSoftDeleted('projects', ['id' => $project->id]);
    }

    /** @test */
    public function navigation_cards_pass_project_id()
    {
        // Arrange
        $project = Project::factory()->create();

        // Act: Load the project show page
        $response = $this->get(route('projects.show', $project));

        // Assert: The page loads successfully
        $response->assertOk();
        
        // Verify all navigation links would have project_id
        // (Vue component handles this client-side with sectionLink helper)
        $response->assertInertia(fn ($page) =>
            $page->component('Projects/Show')
                ->where('project.id', $project->id)
        );

        // Test that clicking navigation actually works by visiting each URL
        $urls = [
            route('initiation.stakeholders', ['project_id' => $project->id]),
            route('resources.team', ['project_id' => $project->id]),
            route('quality.risks', ['project_id' => $project->id]),
            route('resources.gantt', ['project_id' => $project->id]),
            route('reports.analytics', ['project_id' => $project->id]),
        ];

        foreach ($urls as $url) {
            $this->get($url)->assertOk();
        }
    }
}
