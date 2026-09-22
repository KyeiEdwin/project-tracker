<?php

namespace App\Models;

use App\Models\Concerns\SerializesForInertia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChartSeries extends Model
{
    /** @use HasFactory<\Database\Factories\ChartSeriesFactory> */
    use HasFactory;
    use SerializesForInertia;

    protected $table = 'chart_series';

    protected $fillable = [
        'chart_id',
        'name',
        'color',
        'data',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'sort_order' => 'integer',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toInertia(): array
    {
        return [
            'id' => $this->id,
            'chartId' => $this->chart_id,
            'name' => $this->name,
            'color' => $this->color,
            'data' => $this->data ?? [],
            'sortOrder' => (int) $this->sort_order,
        ];
    }

    public function chart(): BelongsTo
    {
        return $this->belongsTo(Chart::class);
    }
}
