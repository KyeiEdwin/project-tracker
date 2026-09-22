<?php

namespace App\Models;

use App\Models\Concerns\SerializesForInertia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskDependency extends Model
{
    /** @use HasFactory<\Database\Factories\TaskDependencyFactory> */
    use HasFactory;
    use SerializesForInertia;

    protected $fillable = [
        'task_id',
        'depends_on_task_id',
        'type',
    ];

    /**
     * @return array<string, mixed>
     */
    public function toInertia(): array
    {
        return [
            'id' => $this->id,
            'taskId' => $this->task_id,
            'dependsOnTaskId' => $this->depends_on_task_id,
            'type' => $this->type,
        ];
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function dependsOn(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'depends_on_task_id');
    }
}
