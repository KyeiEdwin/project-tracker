<?php

namespace App\Models;

use App\Models\Concerns\SerializesForInertia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Project extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectFactory> */
    use HasFactory;
    use SerializesForInertia;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'project_type',
        'priority',
        'status',
        'start_date',
        'due_date',
        'budget',
        'spent',
        'progress',
        'team',
        'client',
        'settings',
        'owner_id',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'due_date' => 'date',
            'budget' => 'decimal:2',
            'spent' => 'decimal:2',
            'progress' => 'integer',
            'settings' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Project $project): void {
            if (blank($project->slug)) {
                $project->slug = Str::slug($project->name).'-'.Str::lower(Str::random(6));
            }
        });
    }

    /**
     * @return array<string, mixed>
     */
    public function toInertia(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'projectType' => $this->project_type,
            'priority' => $this->priority,
            'status' => $this->status,
            'startDate' => $this->start_date?->toDateString(),
            'dueDate' => $this->due_date?->toDateString(),
            'endDate' => $this->due_date?->toDateString(),
            'budget' => $this->budget !== null ? (float) $this->budget : 0,
            'spent' => $this->spent !== null ? (float) $this->spent : 0,
            'progress' => (int) $this->progress,
            'team' => $this->team,
            'client' => $this->client,
            'settings' => $this->settings ?? [],
        ];
    }

    public function kickoffs(): HasMany
    {
        return $this->hasMany(Kickoff::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function stakeholders(): HasMany
    {
        return $this->hasMany(Stakeholder::class);
    }

    public function teamMembers(): BelongsToMany
    {
        return $this->belongsToMany(TeamMember::class)
            ->withPivot('allocation_percent')
            ->withTimestamps();
    }

    public function timeEntries(): HasMany
    {
        return $this->hasMany(TimeEntry::class);
    }

    public function budgetItems(): HasMany
    {
        return $this->hasMany(BudgetItem::class);
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(Milestone::class);
    }

    public function sprints(): HasMany
    {
        return $this->hasMany(Sprint::class);
    }

    public function backlogItems(): HasMany
    {
        return $this->hasMany(BacklogItem::class);
    }

    public function agileDefinitions(): HasMany
    {
        return $this->hasMany(AgileDefinition::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function workflows(): HasMany
    {
        return $this->hasMany(Workflow::class);
    }

    public function qaTests(): HasMany
    {
        return $this->hasMany(QaTest::class);
    }

    public function risks(): HasMany
    {
        return $this->hasMany(Risk::class);
    }

    public function changeLogs(): HasMany
    {
        return $this->hasMany(ChangeLog::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function lessonsLearned(): HasMany
    {
        return $this->hasMany(LessonLearned::class);
    }

    public function charts(): HasMany
    {
        return $this->hasMany(Chart::class);
    }
}
