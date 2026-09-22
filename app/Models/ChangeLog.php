<?php

namespace App\Models;

use App\Models\Concerns\SerializesForInertia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChangeLog extends Model
{
    /** @use HasFactory<\Database\Factories\ChangeLogFactory> */
    use HasFactory;
    use SerializesForInertia;
    use SoftDeletes;

    protected $fillable = [
        'project_id',
        'title',
        'type',
        'requestor',
        'status',
        'impact',
        'description',
        'requested_on',
    ];

    protected function casts(): array
    {
        return [
            'requested_on' => 'date',
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
            'type' => $this->type,
            'requestor' => $this->requestor,
            'status' => $this->status,
            'impact' => $this->impact,
            'description' => $this->description,
            'date' => $this->requested_on?->toDateString(),
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
