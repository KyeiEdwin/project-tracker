<?php

namespace App\Models;

use App\Models\Concerns\SerializesForInertia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Chart extends Model
{
    /** @use HasFactory<\Database\Factories\ChartFactory> */
    use HasFactory;
    use SerializesForInertia;
    use SoftDeletes;

    protected $fillable = [
        'project_id',
        'title',
        'chart_type',
        'data_source',
        'config',
        'is_public',
    ];

    protected function casts(): array
    {
        return [
            'config' => 'array',
            'is_public' => 'boolean',
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
            'title' => $this->title,
            'chartType' => $this->chart_type,
            'dataSource' => $this->data_source,
            'config' => $this->config ?? [],
            'isPublic' => (bool) $this->is_public,
            'series' => $this->relationLoaded('series')
                ? $this->series->map->toInertia()->values()->all()
                : [],
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function series(): HasMany
    {
        return $this->hasMany(ChartSeries::class)->orderBy('sort_order');
    }
}
