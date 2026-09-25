<?php

namespace App\Models;

use App\Models\Concerns\SerializesForInertia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SprintSnapshot extends Model
{
    use SerializesForInertia;

    protected $fillable = [
        'sprint_id',
        'snapshot_date',
        'remaining_points',
        'completed_points',
        'planned_points',
        'scope_change',
    ];

    protected function casts(): array
    {
        return [
            'snapshot_date' => 'date',
            'remaining_points' => 'integer',
            'completed_points' => 'integer',
            'planned_points' => 'integer',
            'scope_change' => 'integer',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toInertia(): array
    {
        return [
            'id' => $this->id,
            'sprintId' => $this->sprint_id,
            'date' => $this->snapshot_date?->toDateString(),
            'remainingPoints' => (int) $this->remaining_points,
            'completedPoints' => (int) $this->completed_points,
            'plannedPoints' => (int) $this->planned_points,
            'scopeChange' => (int) $this->scope_change,
        ];
    }

    public function sprint(): BelongsTo
    {
        return $this->belongsTo(Sprint::class);
    }
}
