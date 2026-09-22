<?php

namespace App\Models;

use App\Models\Concerns\SerializesForInertia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model
{
    /** @use HasFactory<\Database\Factories\ReportFactory> */
    use HasFactory;
    use SerializesForInertia;
    use SoftDeletes;

    protected $fillable = [
        'project_id',
        'name',
        'type',
        'description',
        'icon',
        'color',
        'period_start',
        'period_end',
        'metrics',
        'generated_at',
    ];

    protected function casts(): array
    {
        return [
            'period_start' => 'date',
            'period_end' => 'date',
            'metrics' => 'array',
            'generated_at' => 'datetime',
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
            'description' => $this->description,
            'icon' => $this->icon,
            'color' => $this->color,
            'periodStart' => $this->period_start?->toDateString(),
            'periodEnd' => $this->period_end?->toDateString(),
            'metrics' => $this->metrics ?? [],
            'generatedAt' => $this->generated_at?->toIso8601String(),
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
