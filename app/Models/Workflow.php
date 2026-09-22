<?php

namespace App\Models;

use App\Models\Concerns\SerializesForInertia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Workflow extends Model
{
    /** @use HasFactory<\Database\Factories\WorkflowFactory> */
    use HasFactory;
    use SerializesForInertia;
    use SoftDeletes;

    protected $fillable = [
        'project_id',
        'name',
        'description',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toInertia(): array
    {
        $states = $this->relationLoaded('states')
            ? $this->states->map->toInertia()->values()->all()
            : [];

        return [
            'id' => $this->id,
            'projectId' => $this->project_id,
            'name' => $this->name,
            'description' => $this->description,
            'isDefault' => (bool) $this->is_default,
            'stages' => collect($states)->pluck('name')->values()->all(),
            'states' => $states,
            'projects' => $this->relationLoaded('tasks') ? $this->tasks->pluck('project_id')->unique()->count() : 0,
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function states(): HasMany
    {
        return $this->hasMany(WorkflowState::class)->orderBy('sort_order');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}
