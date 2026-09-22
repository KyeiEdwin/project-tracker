<?php

namespace App\Models;

use App\Models\Concerns\SerializesForInertia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory;
    use SerializesForInertia;
    use SoftDeletes;

    protected $fillable = [
        'project_id',
        'sprint_id',
        'milestone_id',
        'workflow_id',
        'team_member_id',
        'title',
        'description',
        'status',
        'priority',
        'start_date',
        'due_date',
        'duration_days',
        'estimate_hours',
        'progress',
        'kanban_order',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'due_date' => 'date',
            'duration_days' => 'integer',
            'estimate_hours' => 'decimal:2',
            'progress' => 'integer',
            'kanban_order' => 'integer',
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
            'sprintId' => $this->sprint_id,
            'milestoneId' => $this->milestone_id,
            'workflowId' => $this->workflow_id,
            'teamMemberId' => $this->team_member_id,
            'title' => $this->title,
            'name' => $this->title,
            'description' => $this->description,
            'assignee' => $this->assignee?->name,
            'status' => $this->status,
            'priority' => $this->priority,
            'startDate' => $this->start_date?->toDateString(),
            'dueDate' => $this->due_date?->toDateString(),
            'durationDays' => $this->duration_days,
            'estimateHours' => $this->estimate_hours !== null ? (float) $this->estimate_hours : null,
            'progress' => (int) $this->progress,
            'kanbanOrder' => (int) $this->kanban_order,
            'subtasks' => $this->relationLoaded('subtasks')
                ? $this->subtasks->map->toInertia()->values()->all()
                : [],
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

    public function milestone(): BelongsTo
    {
        return $this->belongsTo(Milestone::class);
    }

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(TeamMember::class, 'team_member_id');
    }

    public function subtasks(): HasMany
    {
        return $this->hasMany(Subtask::class)->orderBy('sort_order');
    }

    public function dependencies(): HasMany
    {
        return $this->hasMany(TaskDependency::class);
    }

    public function dependents(): HasMany
    {
        return $this->hasMany(TaskDependency::class, 'depends_on_task_id');
    }

    public function timeEntries(): HasMany
    {
        return $this->hasMany(TimeEntry::class);
    }

    public function backlogItems(): HasMany
    {
        return $this->hasMany(BacklogItem::class);
    }
}
