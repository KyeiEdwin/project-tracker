<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class StoreKickoffRequest extends TrackerFormRequest
{
    public function rules(): array
    {
        return [
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'scheduled_on' => ['required', 'date'],
            'date' => ['nullable', 'date'],
            'location' => ['nullable', 'string', 'max:255'],
            'attendees_count' => ['nullable', 'integer', 'min:0'],
            'attendees' => ['nullable', 'integer', 'min:0'],
            'agenda' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'status' => ['required', 'string', Rule::in(['scheduled', 'completed', 'cancelled'])],
            'objectives' => ['nullable', 'array'],
            'objectives.*.text' => ['required_with:objectives', 'string', 'max:500'],
            'objectives.*.completed' => ['nullable', 'boolean'],
        ];
    }

    protected function passedValidation(): void
    {
        if ($this->filled('date') && ! $this->filled('scheduled_on')) {
            $this->merge(['scheduled_on' => $this->input('date')]);
        }

        if ($this->filled('attendees') && ! $this->filled('attendees_count')) {
            $this->merge(['attendees_count' => $this->input('attendees')]);
        }
    }
}
