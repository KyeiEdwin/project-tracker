<?php

namespace App\Http\Requests;

class UpdateKickoffRequest extends StoreKickoffRequest
{
    public function rules(): array
    {
        $rules = parent::rules();
        $rules['project_id'] = ['sometimes', 'required', 'integer', 'exists:projects,id'];

        return $rules;
    }
}
