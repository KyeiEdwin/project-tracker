<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'team_member_id',
        'body',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function teamMember(): BelongsTo
    {
        return $this->belongsTo(TeamMember::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function toInertia(): array
    {
        return [
            'id' => $this->id,
            'body' => $this->body,
            'createdAt' => $this->created_at?->toIso8601String(),
            'sender' => $this->relationLoaded('teamMember') && $this->teamMember
                ? [
                    'id' => $this->teamMember->id,
                    'name' => $this->teamMember->name,
                    'role' => $this->teamMember->role,
                ]
                : null,
        ];
    }
}
