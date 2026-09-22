<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class StoreBacklogItemRequest extends TrackerFormRequest
{
    public function rules(): array
    {
        return [
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'sprint_id' => ['nullable', 'integer', 'exists:sprints,id'],
            'task_id' => ['nullable', 'integer', 'exists:tasks,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'string', Rule::in(['epic', 'feature', 'story', 'bug', 'spike'])],
            'points' => ['nullable', 'integer', 'min:0'],
            'priority' => ['required', 'string', Rule::in(['low', 'medium', 'high', 'critical'])],
            'status' => ['required', 'string', Rule::in(['backlog', 'ready', 'in-progress', 'done'])],
            'rank' => ['nullable', 'integer'],
        ];
    }
}
