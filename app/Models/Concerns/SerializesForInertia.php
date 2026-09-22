<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait SerializesForInertia
{
    /**
     * @return array<string, mixed>
     */
    public function toInertia(): array
    {
        $payload = [];

        foreach ($this->attributesToArray() as $key => $value) {
            $payload[Str::camel($key)] = $value;
        }

        foreach ($this->getRelations() as $name => $relation) {
            $key = Str::camel($name);

            if ($relation === null) {
                $payload[$key] = null;

                continue;
            }

            if ($relation instanceof EloquentCollection) {
                $payload[$key] = $relation
                    ->map(fn (Model $item) => method_exists($item, 'toInertia') ? $item->toInertia() : $item->toArray())
                    ->values()
                    ->all();

                continue;
            }

            if ($relation instanceof Model) {
                $payload[$key] = method_exists($relation, 'toInertia')
                    ? $relation->toInertia()
                    : $relation->toArray();
            }
        }

        return $payload;
    }
}
