<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class StoreLessonLearnedRequest extends TrackerFormRequest
{
    public function rules(): array
    {
        return [
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', Rule::in(['process', 'technical', 'team', 'scope', 'quality'])],
            'description' => ['nullable', 'string'],
            'impact' => ['required', 'string', Rule::in(['positive', 'negative'])],
            'recorded_on' => ['required', 'date'],
            'date' => ['nullable', 'date'],
        ];
    }

    protected function passedValidation(): void
    {
        if ($this->filled('date') && ! $this->filled('recorded_on')) {
            $this->merge(['recorded_on' => $this->input('date')]);
        }
    }
}
