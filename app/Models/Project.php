<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Project extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectFactory> */
    use HasFactory;

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
     * Shape used by Inertia Vue pages (camelCase, matching existing templates).
     *
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
}
