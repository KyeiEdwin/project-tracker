<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest; extends TrackerFormRequest
{
    public function rules(): array
    {
        return [
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:32'],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:64'],
            'color' => ['nullable', 'string', 'max:32'],
            'period_start' => ['nullable', 'date'],
            'period_end' => ['nullable', 'date', 'after_or_equal:period_start'],
            'metrics' => ['nullable', 'array'],
        ];
    }
}
