<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class StoreSubtaskRequest extends TrackerFormRequest
{
    public function rules(): array
    {
        return [
            'task_id' => ['required', 'integer', 'exists:tasks,id'],
            'team_member_id' => ['nullable', 'integer', 'exists:team_members,id'],
            'title' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string', Rule::in(['pending', 'in-progress', 'completed'])],
            'due_date' => ['nullable', 'date'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
