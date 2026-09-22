<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class StoreQaTestRequest extends TrackerFormRequest
{
    public function rules(): array
    {
        return [
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', Rule::in(['functional', 'performance', 'ui', 'security', 'regression'])],
            'status' => ['required', 'string', Rule::in(['pending', 'passed', 'failed', 'blocked'])],
            'priority' => ['required', 'string', Rule::in(['low', 'medium', 'high', 'critical'])],
            'owner' => ['nullable', 'string', 'max:255'],
            'last_run_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'steps' => ['nullable', 'array'],
            'steps.*.instruction' => ['required', 'string', 'max:500'],
            'steps.*.expected_result' => ['nullable', 'string'],
        ];
    }
}
