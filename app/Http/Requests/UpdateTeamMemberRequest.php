<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateTeamMemberRequest extends TrackerFormRequest
{
    public function rules(): array
    {
        $member = $this->route('team_member');

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => [
                'sometimes',
                'required',
                'email',
                'max:255',
                Rule::unique('team_members', 'email')->ignore($member),
            ],
            'password' => ['sometimes', 'nullable', 'string', 'min:8', 'confirmed'],
            'team_id' => ['sometimes', 'nullable', 'integer', 'exists:teams,id'],
            'role' => ['sometimes', 'required', 'string', Rule::in(['manager', 'developer', 'designer', 'tester', 'analyst'])],
            'department' => ['nullable', 'string', 'max:255'],
            'availability' => ['nullable', 'integer', 'min:0', 'max:100'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', 'string', Rule::in(['active', 'inactive'])],
            'project_ids' => ['nullable', 'array'],
            'project_ids.*' => ['integer', 'exists:projects,id'],
        ];
    }
}
