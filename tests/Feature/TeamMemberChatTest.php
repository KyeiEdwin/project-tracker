<?php

namespace Tests\Feature;

use App\Events\ChatMessageCreated;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\TeamMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class TeamMemberChatTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_team_member_can_send_a_message_to_their_team(): void
    {
        Event::fake([ChatMessageCreated::class]);
        $team = Team::factory()->create();
        $member = TeamMember::factory()->create(['team_id' => $team->id]);

        $response = $this->actingAs($member, 'team_member')
            ->postJson(route('team-member.chat.messages.store'), ['body' => 'Hello team']);

        $response->assertCreated()
            ->assertJsonPath('message.body', 'Hello team')
            ->assertJsonPath('message.sender.id', $member->id);
        $this->assertDatabaseHas('team_messages', [
            'team_id' => $team->id,
            'team_member_id' => $member->id,
            'body' => 'Hello team',
        ]);
        Event::assertDispatched(ChatMessageCreated::class, fn (ChatMessageCreated $event): bool =>
            $event->message->team_id === $team->id
            && $event->message->team_member_id === $member->id
        );
    }

    public function test_active_team_member_can_open_the_dedicated_chat_page(): void
    {
        $team = Team::factory()->create();
        $member = TeamMember::factory()->create(['team_id' => $team->id]);

        $this->actingAs($member, 'team_member')
            ->get(route('team-member.chat'))
            ->assertInertia(fn ($page) => $page
                ->component('TeamMembers/Chat')
                ->where('team.id', $team->id)
                ->where('member.id', $member->id)
                ->where('csrfToken', csrf_token())
                ->has('teamMembers', 1)
                ->has('messages', 0)
            );
    }

    public function test_message_team_and_sender_cannot_be_supplied_by_the_client(): void
    {
        $team = Team::factory()->create();
        $otherTeam = Team::factory()->create();
        $member = TeamMember::factory()->create(['team_id' => $team->id]);
        $otherMember = TeamMember::factory()->create(['team_id' => $otherTeam->id]);

        $this->actingAs($member, 'team_member')
            ->postJson(route('team-member.chat.messages.store'), [
                'team_id' => $otherTeam->id,
                'team_member_id' => $otherMember->id,
                'body' => 'Scoped message',
            ])
            ->assertCreated();

        $this->assertDatabaseHas('team_messages', [
            'team_id' => $team->id,
            'team_member_id' => $member->id,
            'body' => 'Scoped message',
        ]);
        $this->assertDatabaseMissing('team_messages', [
            'team_id' => $otherTeam->id,
            'team_member_id' => $otherMember->id,
            'body' => 'Scoped message',
        ]);
    }

    public function test_dashboard_only_returns_the_authenticated_members_team_chat_data(): void
    {
        $team = Team::factory()->create();
        $otherTeam = Team::factory()->create();
        $member = TeamMember::factory()->create(['team_id' => $team->id]);
        $sameTeamMember = TeamMember::factory()->create(['team_id' => $team->id]);
        $otherTeamMember = TeamMember::factory()->create(['team_id' => $otherTeam->id]);
        $visibleMessage = TeamMessage::query()->create([
            'team_id' => $team->id,
            'team_member_id' => $sameTeamMember->id,
            'body' => 'Visible message',
        ]);
        TeamMessage::query()->create([
            'team_id' => $otherTeam->id,
            'team_member_id' => $otherTeamMember->id,
            'body' => 'Hidden message',
        ]);

        $this->actingAs($member, 'team_member')
            ->get(route('team-member.dashboard'))
            ->assertInertia(fn ($page) => $page
                ->has('teamMembers', 2)
                ->where('teamMembers', function ($members) use ($member, $sameTeamMember): bool {
                    return collect($members)->pluck('id')->sort()->values()->all()
                        === collect([$member->id, $sameTeamMember->id])->sort()->values()->all();
                })
                ->has('messages', 1)
                ->where('messages.0.id', $visibleMessage->id)
                ->where('messages.0.body', 'Visible message')
            );
    }

    public function test_teamless_inactive_and_blank_messages_are_rejected(): void
    {
        $teamlessMember = TeamMember::factory()->create();
        $this->actingAs($teamlessMember, 'team_member')
            ->postJson(route('team-member.chat.messages.store'), ['body' => 'No team'])
            ->assertForbidden();

        $team = Team::factory()->create();
        $inactiveMember = TeamMember::factory()->create([
            'team_id' => $team->id,
            'status' => 'inactive',
        ]);
        $this->actingAs($inactiveMember, 'team_member')
            ->postJson(route('team-member.chat.messages.store'), ['body' => 'Inactive'])
            ->assertForbidden();

        $activeMember = TeamMember::factory()->create(['team_id' => $team->id]);
        $this->actingAs($activeMember, 'team_member')
            ->postJson(route('team-member.chat.messages.store'), ['body' => '   '])
            ->assertStatus(422);
    }

    public function test_member_can_authorize_only_their_teams_private_and_presence_channels(): void
    {
        $team = Team::factory()->create();
        $otherTeam = Team::factory()->create();
        $member = TeamMember::factory()->create(['team_id' => $team->id]);

        $this->actingAs($member, 'team_member')
            ->post('/broadcasting/auth', [
                'socket_id' => '123.456',
                'channel_name' => 'private-team.'.$team->id,
            ])
            ->assertOk();

        $this->actingAs($member, 'team_member')
            ->post('/broadcasting/auth', [
                'socket_id' => '123.456',
                'channel_name' => 'presence-team-presence.'.$team->id,
            ])
            ->assertOk();

        $this->actingAs($member, 'team_member')
            ->post('/broadcasting/auth', [
                'socket_id' => '123.456',
                'channel_name' => 'private-team.'.$otherTeam->id,
            ])
            ->assertForbidden();
    }

    public function test_message_body_is_limited_to_two_thousand_characters(): void
    {
        $team = Team::factory()->create();
        $member = TeamMember::factory()->create(['team_id' => $team->id]);

        $this->actingAs($member, 'team_member')
            ->postJson(route('team-member.chat.messages.store'), ['body' => str_repeat('x', 2001)])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('body');
    }
}
