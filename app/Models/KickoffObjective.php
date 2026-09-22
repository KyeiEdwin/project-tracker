<?php

namespace App\Models;

use App\Models\Concerns\SerializesForInertia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KickoffObjective extends Model
{
    /** @use HasFactory<\Database\Factories\KickoffObjectiveFactory> */
    use HasFactory;
    use SerializesForInertia;

    protected $fillable = [
        'kickoff_id',
        'body',
        'is_completed',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_completed' => 'boolean',
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
            'kickoffId' => $this->kickoff_id,
            'text' => $this->body,
            'completed' => (bool) $this->is_completed,
            'sortOrder' => (int) $this->sort_order,
        ];
    }

    public function kickoff(): BelongsTo
    {
        return $this->belongsTo(Kickoff::class);
    }
}
