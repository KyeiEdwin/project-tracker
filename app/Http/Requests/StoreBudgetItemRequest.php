<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class StoreBudgetItemRequest extends TrackerFormRequest
{
    public function rules(): array
    {
        return [
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'category' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'allocated' => ['required', 'numeric', 'min:0'],
            'spent' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', 'string', Rule::in(['on-track', 'under', 'over'])],
        ];
    }
}
