<?php

namespace App\Http\Requests;

use App\Models\Sprint;
use Illuminate\Validation\Rule;

class StoreSprintRequest extends TrackerFormRequest
{
    public function rules(): array
    {
        return [
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'name' => ['required', 'string', 'max:255'],
            'goal' => ['nullable', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'status' => ['required', 'string', Rule::in(['planned', 'active', 'completed'])],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Only validate for 'active' status
            if ($this->input('status') === 'active') {
                $projectId = $this->input('project_id');
                
                // Check if there's already an active sprint for this project
                $activeSprintExists = Sprint::where('project_id', $projectId)
                    ->where('status', 'active')
                    ->when($this->route('sprint'), function ($query) {
                        // Exclude current sprint if updating
                        $query->where('id', '!=', $this->route('sprint')->id);
                    })
                    ->exists();

                if ($activeSprintExists) {
                    $validator->errors()->add(
                        'status',
                        'Cannot start sprint: Another sprint is already active for this project. Please close the active sprint first.'
                    );
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'end_date.after_or_equal' => 'The sprint end date must be on or after the start date.',
            'status.in' => 'The status must be one of: planned, active, or completed.',
        ];
    }
}
