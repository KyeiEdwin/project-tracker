<?php

namespace App\Models;

use App\Models\Concerns\SerializesForInertia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Risk extends Model
{
    /** @use HasFactory<\Database\Factories\RiskFactory> */
    use HasFactory;
    use SerializesForInertia;
    use SoftDeletes;

    protected $fillable = [
        'project_id',
        'title',
        'category',
        'probability',
        'impact',
        'status',
        'owner',
        'mitigation',
    ];

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
            'category' => $this->category,
            'probability' => $this->probability,
            'impact' => $this->impact,
            'status' => $this->status,
            'owner' => $this->owner,
            'mitigation' => $this->mitigation,
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
