<?php

namespace App\Models;

use App\Models\Concerns\SerializesForInertia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class WorkflowState extends Model
{
    /** @use HasFactory<\Database\Factories\WorkflowStateFactory> */
    use HasFactory;
    use SerializesForInertia;

    protected $fillable = [
        'workflow_id',
        'name',
        'slug',
        'color',
        'sort_order',
        'is_initial',
        'is_final',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_initial' => 'boolean',
            'is_final' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (WorkflowState $state): void {
            if (blank($state->slug)) {
                $state->slug = Str::slug($state->name);
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
            'workflowId' => $this->workflow_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'color' => $this->color,
            'sortOrder' => (int) $this->sort_order,
            'isInitial' => (bool) $this->is_initial,
            'isFinal' => (bool) $this->is_final,
        ];
    }

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class);
    }
}
