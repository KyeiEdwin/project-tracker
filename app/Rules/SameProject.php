<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class SameProject implements ValidationRule
{
    protected string $table;
    protected int $projectId;
    protected string $relationName;

    /**
     * Create a new rule instance.
     *
     * @param string $table The table to check (e.g., 'sprints', 'backlog_items')
     * @param int $projectId The project ID to validate against
     * @param string $relationName Human-readable name for error messages
     */
    public function __construct(string $table, int $projectId, string $relationName = 'related item')
    {
        $this->table = $table;
        $this->projectId = $projectId;
        $this->relationName = $relationName;
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$value) {
            return; // Allow null values (use 'required' rule separately if needed)
        }

        $exists = DB::table($this->table)
            ->where('id', $value)
            ->where('project_id', $this->projectId)
            ->exists();

        if (!$exists) {
            $fail("The {$this->relationName} must belong to the same project.");
        }
    }
}
