<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Inertia\Inertia;
use Inertia\Response;

class TeamMemberAccountController extends Controller
{
    public function profile(Request $request): Response
    {
        $member = $request->user('team_member')->load('team');

        return Inertia::render('TeamMembers/Profile', [
            'member' => $member->toInertia(),
            'team' => $member->team?->only(['id', 'name', 'slug']),
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $member = $request->user('team_member');
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('team_members', 'email')->ignore($member->id),
                Rule::unique('users', 'email'),
            ],
        ]);

        $member->update($data);

        return back()->with('success', 'Profile updated.');
    }

    public function settings(Request $request): Response
    {
        $member = $request->user('team_member')->load('team');

        return Inertia::render('TeamMembers/Settings', [
            'member' => $member->toInertia(),
            'team' => $member->team?->only(['id', 'name', 'slug']),
        ]);
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $member = $request->user('team_member');
        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
        ]);

        if (! Hash::check($data['current_password'], $member->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'The current password is incorrect.',
            ]);
        }

        $member->update(['password' => $data['password']]);

        return back()->with('success', 'Password updated.');
    }
}
