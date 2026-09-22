<?php

namespace App\Http\Requests;

class StoreWorkflowRequest extends TrackerFormRequest
{
    public function rules(): array
    {
        return [
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_default' => ['nullable', 'boolean'],
            'states' => ['nullable', 'array', 'min:1'],
            'states.*.name' => ['required', 'string', 'max:64'],
            'states.*.color' => ['nullable', 'string', 'max:32'],
            'states.*.is_initial' => ['nullable', 'boolean'],
            'states.*.is_final' => ['nullable', 'boolean'],
        ];
    }
}
