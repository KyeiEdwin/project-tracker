<?php

namespace Tests\Feature;

use App\Models\TeamMember;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamMemberAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_team_member_can_authenticate_with_member_credentials(): void
    {
        $member = TeamMember::factory()->create([
            'email' => 'developer@example.test',
            'password' => 'password',
            'role' => 'developer',
        ]);

        $this->post(route('login.store'), [
            'email' => $member->email,
            'password' => 'password',
        ])->assertRedirect(route('team-member.dashboard'));

        $this->assertAuthenticatedAs($member, 'team_member');

        $this->get(route('team-member.dashboard'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->where('role', 'developer')
                ->where('metrics.totalTasks', 0)
            );
    }
}
