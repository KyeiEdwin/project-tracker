<?php

namespace App\Models;

use App\Models\Concerns\SerializesForInertia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SprintEvent extends Model
{
    use SerializesForInertia;

    // Only track created_at, not updated_at
    const UPDATED_AT = null;

    protected $fillable = [
        'sprint_id',
        'event_type',
        'user_id',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'created_at' => 'datetime',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toInertia(): array
    {
        return [
            'id' => $this->id,
            'sprintId' => $this->sprint_id,
            'eventType' => $this->event_type,
            'userId' => $this->user_id,
            'userName' => $this->user?->name,
            'metadata' => $this->metadata,
            'createdAt' => $this->created_at?->toIso8601String(),
        ];
    }

    public function sprint(): BelongsTo
    {
        return $this->belongsTo(Sprint::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
