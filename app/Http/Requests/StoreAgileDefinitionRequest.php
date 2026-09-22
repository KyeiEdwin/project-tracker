<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class StoreAgileDefinitionRequest extends TrackerFormRequest
{
    public function rules(): array
    {
        return [
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
            'kind' => ['required', 'string', Rule::in(['ready', 'done'])],
            'body' => ['required', 'string', 'max:500'],
            'text' => ['nullable', 'string', 'max:500'],
            'is_checked' => ['nullable', 'boolean'],
            'checked' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    protected function passedValidation(): void
    {
        if ($this->filled('text') && ! $this->filled('body')) {
            $this->merge(['body' => $this->input('text')]);
        }

        if ($this->exists('checked') && ! $this->exists('is_checked')) {
            $this->merge(['is_checked' => $this->boolean('checked')]);
        }
    }
}
