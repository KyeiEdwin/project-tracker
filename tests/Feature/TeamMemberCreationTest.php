<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class TeamMemberCreationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:fresh', ['--force' => true]);
    }

    public function test_team_member_store_persists_valid_form_data(): void
    {
        $response = $this->post('/team-members', [
            'name' => 'Taylor Morgan',
            'email' => 'taylor@example.test',
            'role' => 'Backend Engineer',
            'department' => 'Engineering',
            'availability' => 80,
            'hourly_rate' => 125,
            'status' => 'active',
        ]);

        $response->assertRedirect('/resources/team');

        $this->assertDatabaseHas('team_members', [
            'name' => 'Taylor Morgan',
            'email' => 'taylor@example.test',
            'role' => 'Backend Engineer',
            'availability' => 80,
            'status' => 'active',
        ]);
    }
}
