<?php

namespace App\Models;

use App\Models\Concerns\SerializesForInertia;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sprint extends Model
{
    /** @use HasFactory<\Database\Factories\SprintFactory> */
    use HasFactory;
    use SerializesForInertia;
    use SoftDeletes;

    protected $fillable = [
        'project_id',
        'name',
        'goal',
        'start_date',
        'end_date',
        'status',
        // Note: planned_points and completed_points are now computed properties
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    /**
     * Computed property: Sum of points from all backlog items in sprint
     */
    protected function plannedPoints(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->backlogItems()->sum('points') ?? 0
        );
    }

    /**
     * Computed property: Sum of points from done backlog items in sprint
     */
    protected function completedPoints(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->backlogItems()->where('status', 'done')->sum('points') ?? 0
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toInertia(): array
    {
        $daysRemaining = $this->end_date
            ? max(0, now()->startOfDay()->diffInDays($this->end_date->startOfDay(), false))
            : 0;

        return [
            'id' => $this->id,
            'projectId' => $this->project_id,
            'project' => $this->project?->name,
            'name' => $this->name,
            'goal' => $this->goal,
            'startDate' => $this->start_date?->toDateString(),
            'endDate' => $this->end_date?->toDateString(),
            'status' => $this->status,
            'points' => (int) $this->planned_points,
            'completed' => (int) $this->completed_points,
            'daysRemaining' => (int) $daysRemaining,
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function backlogItems(): HasMany
    {
        return $this->hasMany(BacklogItem::class);
    }

    public function snapshots(): HasMany
    {
        return $this->hasMany(SprintSnapshot::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(SprintEvent::class)->orderBy('created_at', 'desc');
    }
}
