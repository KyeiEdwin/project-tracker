<?php

namespace App\Models;

use App\Models\Concerns\SerializesForInertia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BacklogItem extends Model
{
    /** @use HasFactory<\Database\Factories\BacklogItemFactory> */
    use HasFactory;
    use SerializesForInertia;
    use SoftDeletes;

    protected $fillable = [
        'project_id',
        'sprint_id',
        'task_id',
        'title',
        'description',
        'type',
        'points',
        'priority',
        'status',
        'rank',
    ];

    protected function casts(): array
    {
        return [
            'points' => 'integer',
            'rank' => 'integer',
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
            'sprintId' => $this->sprint_id,
            'taskId' => $this->task_id,
            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type,
            'points' => (int) $this->points,
            'priority' => $this->priority,
            'status' => $this->status,
            'rank' => (int) $this->rank,
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function sprint(): BelongsTo
    {
        return $this->belongsTo(Sprint::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }
}
