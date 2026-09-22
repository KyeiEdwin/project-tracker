<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class StoreChangeLogRequest extends TrackerFormRequest
{
    public function rules(): array
    {
        return [
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', Rule::in(['feature', 'schedule', 'scope', 'budget', 'process'])],
            'requestor' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string', Rule::in(['pending', 'approved', 'rejected'])],
            'impact' => ['required', 'string', Rule::in(['low', 'medium', 'high'])],
            'description' => ['nullable', 'string'],
            'requested_on' => ['required', 'date'],
            'date' => ['nullable', 'date'],
        ];
    }

    protected function passedValidation(): void
    {
        if ($this->filled('date') && ! $this->filled('requested_on')) {
            $this->merge(['requested_on' => $this->input('date')]);
        }
    }
}
