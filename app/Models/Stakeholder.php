<?php

namespace App\Models;

use App\Models\Concerns\SerializesForInertia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stakeholder extends Model
{
    /** @use HasFactory<\Database\Factories\StakeholderFactory> */
    use HasFactory;
    use SerializesForInertia;
    use SoftDeletes;

    protected $fillable = [
        'project_id',
        'name',
        'email',
        'role',
        'department',
        'organization',
        'influence',
        'interest',
        'notes',
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
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'department' => $this->department,
            'organization' => $this->organization,
            'influence' => $this->influence,
            'interest' => $this->interest,
            'notes' => $this->notes,
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
