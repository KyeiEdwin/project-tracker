<?php

namespace App\Http\Requests\Concerns;

use Illuminate\Support\Str;

trait NormalizesCamelCaseInput
{
    protected function prepareForValidation(): void
    {
        $merge = [];

        foreach ($this->all() as $key => $value) {
            if (! is_string($key)) {
                continue;
            }

            $snake = Str::snake($key);

            if ($snake !== $key && ! $this->exists($snake)) {
                $merge[$snake] = $value;
            }
        }

        if ($merge !== []) {
            $this->merge($merge);
        }
    }
}
