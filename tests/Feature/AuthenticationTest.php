<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
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
}