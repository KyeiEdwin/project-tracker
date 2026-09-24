<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateTeamMemberTaskStatusRequest extends TrackerFormRequest
{
    public function rules(): array
    {
        return [
            'status' => [
                'required',
                'string',
                Rule::in(['pending', 'todo', 'in-progress', 'completed', 'done']),
            ],
        ];
    }
}