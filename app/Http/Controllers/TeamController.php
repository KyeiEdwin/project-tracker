<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:teams,slug'],
            'description' => ['nullable', 'string'],
        ]);

        $team = Team::query()->create($data);

        return back()->with('success', "Team {$team->name} created.");
    }
}