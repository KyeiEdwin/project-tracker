<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class StoreSprintRequest extends TrackerFormRequest
{
    public function rules(): array
    {
        return [
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'name' => ['required', 'string', 'max:255'],
            'goal' => ['nullable', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'status' => ['required', 'string', Rule::in(['planned', 'active', 'completed'])],
            'planned_points' => ['nullable', 'integer', 'min:0'],
            'completed_points' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
