<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class StoreTaskRequest extends TrackerFormRequest
{
    public function rules(): array
    {
        return [
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'sprint_id' => ['nullable', 'integer', 'exists:sprints,id'],
            'milestone_id' => ['nullable', 'integer', 'exists:milestones,id'],
            'workflow_id' => ['nullable', 'integer', 'exists:workflows,id'],
            'team_member_id' => ['nullable', 'integer', 'exists:team_members,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'string', Rule::in(['pending', 'backlog', 'todo', 'in-progress', 'completed', 'done'])],
            'priority' => ['required', 'string', Rule::in(['low', 'medium', 'high', 'critical'])],
            'start_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'duration_days' => ['nullable', 'integer', 'min:0'],
            'estimate_hours' => ['nullable', 'numeric', 'min:0'],
            'progress' => ['nullable', 'integer', 'min:0', 'max:100'],
            'kanban_order' => ['nullable', 'integer', 'min:0'],
            'dependencies' => ['nullable', 'array'],
            'dependencies.*' => ['integer', 'exists:tasks,id'],
        ];
    }
}
