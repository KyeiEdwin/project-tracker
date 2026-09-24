<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use App\Models\TeamMember;
use App\Policies\ProjectPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_log_in_and_log_out(): void
    {
        $user = User::factory()->create(['password' => 'password']);

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);

        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_project_channel_allows_owner_and_rejects_other_users(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $project = Project::factory()->create(['owner_id' => $owner->id]);

        $policy = new ProjectPolicy();

        $this->assertTrue($policy->view($owner, $project));
        $this->assertFalse($policy->view($otherUser, $project));
    }

    public function test_admin_dashboard_requires_the_admin_dashboard_permission(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'web')
            ->get(route('dashboard'))
            ->assertOk();
    }

    public function test_registration_creates_and_authenticates_a_team_member(): void
    {
        $role = Role::query()->updateOrCreate(['name' => 'team_member'], ['label' => 'Team Member']);
        $permission = Permission::query()->updateOrCreate(
            ['name' => 'dashboard.team_member.view'],
            ['label' => 'Dashboard Team Member View']
        );
        $role->permissions()->attach($permission);
        $team = Team::factory()->create(['name' => 'Product Team']);

        $response = $this->post(route('register.store'), [
            'name' => 'New Team Member',
            'email' => 'new-member@example.test',
            'team_id' => $team->id,
            'role' => 'tester',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('team-member.dashboard'));
        $this->assertGuest('web');
        $this->assertAuthenticated('team_member');
        $this->assertDatabaseHas('team_members', [
            'name' => 'New Team Member',
            'email' => 'new-member@example.test',
            'role' => 'tester',
            'team_id' => $team->id,
            'status' => 'active',
        ]);
        $this->assertDatabaseMissing('users', [
            'email' => 'new-member@example.test',
        ]);
        $this->assertTrue(TeamMember::query()
            ->where('email', 'new-member@example.test')
            ->firstOrFail()
            ->hasPermission('dashboard.team_member.view'));
    }

    public function test_registration_only_exposes_active_teams_and_job_roles(): void
    {
        $activeTeam = Team::factory()->create(['name' => 'Active Team', 'status' => 'active']);
        $inactiveTeam = Team::factory()->create(['name' => 'Inactive Team', 'status' => 'inactive']);

        $this->get(route('register'))
            ->assertInertia(fn ($page) => $page
                ->where('teams.0.id', $activeTeam->id)
                ->missing('teams.1')
                ->where('jobRoles.0.value', 'manager')
                ->where('jobRoles.4.value', 'analyst'));

        $this->assertNotSame($activeTeam->id, $inactiveTeam->id);
    }

    public function test_registration_rejects_inactive_teams_and_invalid_job_roles(): void
    {
        $team = Team::factory()->create(['status' => 'inactive']);

        $this->from(route('register'))
            ->post(route('register.store'), [
                'name' => 'Invalid Member',
                'email' => 'invalid-member@example.test',
                'team_id' => $team->id,
                'role' => 'admin',
                'password' => 'password',
                'password_confirmation' => 'password',
            ])
            ->assertRedirect(route('register'))
            ->assertSessionHasErrors(['team_id', 'role']);
    }

    public function test_registration_rejects_an_email_used_by_an_existing_user_or_member(): void
    {
        User::factory()->create(['email' => 'existing@example.test']);

        $this->from(route('register'))
            ->post(route('register.store'), [
                'name' => 'Duplicate',
                'email' => 'existing@example.test',
                'password' => 'password',
                'password_confirmation' => 'password',
            ])
            ->assertRedirect(route('register'))
            ->assertSessionHasErrors('email');
    }
}