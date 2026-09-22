<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class StoreDocumentRequest extends TrackerFormRequest
{
    public function rules(): array
    {
        return [
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', Rule::in(['pdf', 'doc', 'excel', 'design', 'other'])],
            'file' => ['nullable', 'file', 'max:20480'],
            'path' => ['nullable', 'string', 'max:1024'],
            'disk' => ['nullable', 'string', 'max:32'],
            'size_bytes' => ['nullable', 'integer', 'min:0'],
            'mime_type' => ['nullable', 'string', 'max:128'],
            'uploaded_by' => ['nullable', 'string', 'max:255'],
        ];
    }
}
