<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class StoreRiskRequest extends TrackerFormRequest
{
    public function rules(): array
    {
        return [
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', Rule::in(['resource', 'scope', 'technical', 'external', 'financial', 'schedule'])],
            'probability' => ['required', 'string', Rule::in(['low', 'medium', 'high'])],
            'impact' => ['required', 'string', Rule::in(['low', 'medium', 'high'])],
            'status' => ['required', 'string', Rule::in(['open', 'mitigating', 'closed'])],
            'owner' => ['nullable', 'string', 'max:255'],
            'mitigation' => ['nullable', 'string'],
        ];
    }
}
