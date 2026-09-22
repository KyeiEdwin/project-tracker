<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class StoreMilestoneRequest extends TrackerFormRequest
{
    public function rules(): array
    {
        return [
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_date' => ['required', 'date'],
            'date' => ['nullable', 'date'],
            'status' => ['required', 'string', Rule::in(['upcoming', 'in-progress', 'completed'])],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    protected function passedValidation(): void
    {
        if ($this->filled('date') && ! $this->filled('due_date')) {
            $this->merge(['due_date' => $this->input('date')]);
        }
    }
}
