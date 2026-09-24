<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\TeamMember;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TeamMemberAccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_team_member_can_view_and_update_profile(): void
    {
        $member = TeamMember::factory()->create(['password' => 'password']);

        $this->actingAs($member, 'team_member')
            ->get(route('team-member.profile'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('TeamMembers/Profile')
                ->where('member.email', $member->email));

        $this->actingAs($member, 'team_member')
            ->put(route('team-member.profile.update'), [
                'name' => 'Updated Member',
                'email' => 'updated-member@example.test',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('team_members', [
            'id' => $member->id,
            'name' => 'Updated Member',
            'email' => 'updated-member@example.test',
        ]);
    }

    public function test_team_member_can_view_settings_and_change_password(): void
    {
        $member = TeamMember::factory()->create(['password' => 'password']);

        $this->actingAs($member, 'team_member')
            ->get(route('team-member.settings'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('TeamMembers/Settings'));

        $this->actingAs($member, 'team_member')
            ->patch(route('team-member.settings.password'), [
                'current_password' => 'password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ])
            ->assertRedirect();

        $this->assertTrue(Hash::check('new-password', $member->fresh()->password));
    }

    public function test_team_member_account_pages_require_their_permissions(): void
    {
        $member = TeamMember::factory()->create();
        $member->memberRole->permissions()->detach(
            Permission::query()->whereIn('name', ['profile.view', 'profile.edit', 'setting.view'])->pluck('id')
        );

        $this->actingAs($member, 'team_member')
            ->get(route('team-member.profile'))
            ->assertForbidden();
        $this->actingAs($member, 'team_member')
            ->put(route('team-member.profile.update'), [
                'name' => 'Updated Member',
                'email' => 'updated-member@example.test',
            ])
            ->assertForbidden();
        $this->actingAs($member, 'team_member')
            ->get(route('team-member.settings'))
            ->assertForbidden();
    }

    public function test_team_member_password_change_requires_the_current_password(): void
    {
        $member = TeamMember::factory()->create(['password' => 'password']);

        $this->actingAs($member, 'team_member')
            ->from(route('team-member.settings'))
            ->patch(route('team-member.settings.password'), [
                'current_password' => 'wrong-password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ])
            ->assertRedirect(route('team-member.settings'))
            ->assertSessionHasErrors('current_password');

        $this->assertTrue(Hash::check('password', $member->fresh()->password));
    }
}
