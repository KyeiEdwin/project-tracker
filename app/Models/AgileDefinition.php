<?php

namespace App\Models;

use App\Models\Concerns\SerializesForInertia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgileDefinition extends Model
{
    /** @use HasFactory<\Database\Factories\AgileDefinitionFactory> */
    use HasFactory;
    use SerializesForInertia;
    use SoftDeletes;

    protected $fillable = [
        'project_id',
        'kind',
        'body',
        'is_checked',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_checked' => 'boolean',
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
            'projectId' => $this->project_id,
            'kind' => $this->kind,
            'text' => $this->body,
            'checked' => (bool) $this->is_checked,
            'sortOrder' => (int) $this->sort_order,
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
