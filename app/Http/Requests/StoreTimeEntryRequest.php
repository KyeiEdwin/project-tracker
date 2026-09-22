<?php

namespace App\Http\Requests;

class StoreTimeEntryRequest extends TrackerFormRequest
{
    public function rules(): array
    {
        return [
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'team_member_id' => ['required', 'integer', 'exists:team_members,id'],
            'task_id' => ['nullable', 'integer', 'exists:tasks,id'],
            'work_date' => ['required', 'date'],
            'date' => ['nullable', 'date'],
            'hours' => ['required', 'numeric', 'min:0.25', 'max:24'],
            'description' => ['nullable', 'string', 'max:255'],
            'is_billable' => ['nullable', 'boolean'],
        ];
    }

    protected function passedValidation(): void
    {
        if ($this->filled('date') && ! $this->filled('work_date')) {
            $this->merge(['work_date' => $this->input('date')]);
        }
    }
}
