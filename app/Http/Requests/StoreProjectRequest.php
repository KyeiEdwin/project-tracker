<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class StoreProjectRequest extends TrackerFormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => ['required', 'string', Rule::in(['low', 'medium', 'high', 'critical'])],
            'status' => ['required', 'string', Rule::in(['planning', 'in-progress', 'on-hold', 'completed'])],
            'due_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'start_date' => ['nullable', 'date'],
            'progress' => ['nullable', 'integer', 'min:0', 'max:100'],
            'project_type' => ['nullable', 'string', 'max:32'],
            'team' => ['nullable', 'string', 'max:255'],
            'team_id' => ['nullable', 'integer', 'exists:teams,id'],
            'client' => ['nullable', 'string', 'max:255'],
            'phases' => ['nullable'],
            'milestones' => ['nullable', 'string'],
            'deliverables' => ['nullable', 'string'],
            'sprint_duration' => ['nullable', 'string'],
            'sprint_goal' => ['nullable', 'string'],
            'velocity' => ['nullable'],
            'methodology' => ['nullable', 'string'],
            'sprint_length' => ['nullable', 'string'],
            'phase_count' => ['nullable'],
        ];
    }
}
