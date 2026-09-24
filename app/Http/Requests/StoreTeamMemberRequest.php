<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class StoreTeamMemberRequest extends TrackerFormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:team_members,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'team_id' => ['nullable', 'integer', 'exists:teams,id'],
            'role' => ['required', 'string', Rule::in(['manager', 'developer', 'designer', 'tester', 'analyst'])],
            'department' => ['nullable', 'string', 'max:255'],
            'availability' => ['nullable', 'integer', 'min:0', 'max:100'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', 'string', Rule::in(['active', 'inactive'])],
            'project_ids' => ['nullable', 'array'],
            'project_ids.*' => ['integer', 'exists:projects,id'],
        ];
    }
}
