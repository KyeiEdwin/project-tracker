<?php

namespace App\Models;

use App\Models\Concerns\SerializesForInertia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kickoff extends Model
{
    /** @use HasFactory<\Database\Factories\KickoffFactory> */
    use HasFactory;
    use SerializesForInertia;
    use SoftDeletes;

    protected $fillable = [
        'project_id',
        'scheduled_on',
        'location',
        'attendees_count',
        'agenda',
        'notes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_on' => 'date',
            'attendees_count' => 'integer',
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
            'date' => $this->scheduled_on?->toDateString(),
            'location' => $this->location,
            'attendees' => (int) $this->attendees_count,
            'agenda' => $this->agenda,
            'notes' => $this->notes,
            'status' => $this->status,
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function objectives(): HasMany
    {
        return $this->hasMany(KickoffObjective::class)->orderBy('sort_order');
    }
}
