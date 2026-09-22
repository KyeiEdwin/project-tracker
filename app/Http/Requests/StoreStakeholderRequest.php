<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class StoreStakeholderRequest extends TrackerFormRequest
{
    public function rules(): array
    {
        return [
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'role' => ['required', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'organization' => ['nullable', 'string', 'max:255'],
            'influence' => ['required', 'string', Rule::in(['low', 'medium', 'high'])],
            'interest' => ['required', 'string', Rule::in(['low', 'medium', 'high'])],
            'notes' => ['nullable', 'string'],
        ];
    }
}
