<?php

namespace App\Http\Controllers;

use App\Events\ChatMessageCreated;
use App\Models\TeamMember;
use Illuminate\Broadcasting\BroadcastException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TeamMemberChatController extends Controller
{
    public function index(Request $request): Response
    {
        /** @var TeamMember $member */
        $member = $request->user('team_member');
        abort_unless($member->hasPermission('dashboard.team_member.view'), 403);

        $member->load('team');

        $teamMembers = $member->team
            ? $member->team->members()
                ->where('status', 'active')
                ->orderBy('name')
                ->get(['id', 'name', 'role', 'status'])
            : collect();
        $messages = $member->team
            ? $member->team->messages()
                ->with('teamMember:id,name,role')
                ->latest()
                ->limit(50)
                ->get()
                ->reverse()
                ->values()
            : collect();

        return Inertia::render('TeamMembers/Chat', [
            'member' => $member->toInertia(),
            'csrfToken' => csrf_token(),
            'team' => $member->team ? [
                'id' => $member->team->id,
                'name' => $member->team->name,
                'slug' => $member->team->slug,
                'description' => $member->team->description,
                'status' => $member->team->status,
            ] : null,
            'teamMembers' => $teamMembers->map(fn (TeamMember $teamMember) => [
                'id' => $teamMember->id,
                'name' => $teamMember->name,
                'role' => $teamMember->role,
                'status' => $teamMember->status,
            ])->values(),
            'messages' => $messages->map->toInertia()->values(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        /** @var TeamMember $member */
        $member = $request->user('team_member');
        abort_unless($member->status === 'active' && $member->team_id, 403);

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $body = trim($validated['body']);
        abort_if($body === '', 422, 'Message body cannot be empty.');

        $message = $member->messages()->create([
            'team_id' => $member->team_id,
            'body' => $body,
        ]);
        $message->load('teamMember:id,name,role');

        try {
            broadcast(new ChatMessageCreated($message))->toOthers();
        } catch (BroadcastException) {
        }

        return response()->json([
            'message' => $message->toInertia(),
        ], 201);
    }
}
