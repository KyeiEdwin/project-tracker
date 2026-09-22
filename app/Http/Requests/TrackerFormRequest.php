<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\NormalizesCamelCaseInput;
use Illuminate\Foundation\Http\FormRequest;

abstract class TrackerFormRequest extends FormRequest
{
    use NormalizesCamelCaseInput;

    public function authorize(): bool
    {
        return true;
    }
}
