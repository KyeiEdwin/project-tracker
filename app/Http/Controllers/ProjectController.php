<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Projects/Index');
    }

    public function create(): Response
    {
        return Inertia::render('Projects/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|string',
            'status' => 'required|string',
            'dueDate' => 'nullable|date',
            'budget' => 'nullable|numeric',
        ]);

        return redirect()->route('projects.index')->with('success', 'Project created successfully.');
    }

    public function show(string|int $id): Response
    {
        return Inertia::render('Projects/Show', [
            'id' => (int) $id,
        ]);
    }
}
