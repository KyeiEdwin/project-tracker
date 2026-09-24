<?php

namespace App\Models;

use App\Models\Concerns\SerializesForInertia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class TeamMember extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\TeamMemberFactory> */
    use HasFactory;
    use SerializesForInertia;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'team_id',
        'role_id',
        'email',
        'password',
        'role',
        'department',
        'availability',
        'hourly_rate',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'hourly_rate',
    ];

    protected function casts(): array
    {
        return [
            'availability' => 'integer',
            'hourly_rate' => 'decimal:2',
            'password' => 'hashed',
        ];
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(TeamMessage::class);
    }

    public function memberRole(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function hasPermission(string $permission): bool
    {
        return $this->status === 'active'
            && $this->memberRole?->permissions()->where('name', $permission)->exists();
    }

    /**
     * @return array<string, mixed>
     */
    public function toInertia(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'department' => $this->department,
            'availability' => (int) $this->availability,
            'status' => $this->status,
            'teamId' => $this->team_id,
            'roleId' => $this->role_id,
            'team' => $this->relationLoaded('team') ? $this->team?->name : null,
            'projects' => $this->relationLoaded('projects') ? $this->projects->count() : 0,
            'projectIds' => $this->relationLoaded('projects') ? $this->projects->pluck('id')->values()->all() : [],
        ];
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class)
            ->withPivot('allocation_percent')
            ->withTimestamps();
    }

    public function timeEntries(): HasMany
    {
        return $this->hasMany(TimeEntry::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function assignedTasks(): HasMany
    {
        return $this->tasks()
            ->whereIn('project_id', $this->projects()->select('projects.id'))
            ->where('team_member_id', $this->getKey());
    }

    public function subtasks(): HasMany
    {
        return $this->hasMany(Subtask::class);
    }
}
