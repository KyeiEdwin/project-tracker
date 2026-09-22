<?php

namespace App\Models;

use App\Models\Concerns\SerializesForInertia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LessonLearned extends Model
{
    /** @use HasFactory<\Database\Factories\LessonLearnedFactory> */
    use HasFactory;
    use SerializesForInertia;
    use SoftDeletes;

    protected $table = 'lessons_learned';

    protected $fillable = [
        'project_id',
        'title',
        'category',
        'description',
        'impact',
        'recorded_on',
    ];

    protected function casts(): array
    {
        return [
            'recorded_on' => 'date',
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
            'category' => $this->category,
            'description' => $this->description,
            'impact' => $this->impact,
            'date' => $this->recorded_on?->toDateString(),
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
