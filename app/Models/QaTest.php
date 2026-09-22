<?php

namespace App\Models;

use App\Models\Concerns\SerializesForInertia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class QaTest extends Model
{
    /** @use HasFactory<\Database\Factories\QaTestFactory> */
    use HasFactory;
    use SerializesForInertia;
    use SoftDeletes;

    protected $fillable = [
        'project_id',
        'name',
        'type',
        'status',
        'priority',
        'owner',
        'last_run_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'last_run_at' => 'datetime',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toInertia(): array
    {
        return [
            'id' => $this->id,
            'projectId' => $this->project_id,
            'project' => $this->project?->name,
            'name' => $this->name,
            'type' => $this->type,
            'status' => $this->status,
            'priority' => $this->priority,
            'owner' => $this->owner,
            'lastRun' => $this->last_run_at?->toDateString() ?? '-',
            'notes' => $this->notes,
            'steps' => $this->relationLoaded('steps')
                ? $this->steps->map->toInertia()->values()->all()
                : [],
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function steps(): HasMany
    {
        return $this->hasMany(QaTestStep::class)->orderBy('sort_order');
    }
}
