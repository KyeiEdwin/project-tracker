<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class StoreChartRequest extends TrackerFormRequest
{
    public function rules(): array
    {
        return [
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
            'title' => ['required', 'string', 'max:255'],
            'chart_type' => ['required', 'string', Rule::in(['bar', 'line', 'pie', 'area', 'burndown', 'gantt'])],
            'data_source' => ['nullable', 'string', 'max:64'],
            'config' => ['nullable', 'array'],
            'is_public' => ['nullable', 'boolean'],
            'series' => ['nullable', 'array'],
            'series.*.name' => ['required', 'string', 'max:255'],
            'series.*.color' => ['nullable', 'string', 'max:32'],
            'series.*.data' => ['nullable', 'array'],
        ];
    }
}
