<?php

namespace App\Models;

use App\Models\Concerns\SerializesForInertia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BacklogItem extends Model
{
    /** @use HasFactory<\Database\Factories\BacklogItemFactory> */
    use HasFactory;
    use SerializesForInertia;
    use SoftDeletes;

    protected $fillable = [
        'project_id',
        'parent_id',
        'sprint_id',
        'task_id',
        'title',
        'description',
        'type',
        'points',
        'priority',
        'status',
        'rank',
    ];

    protected function casts(): array
    {
        return [
            'points' => 'integer',
            'rank' => 'integer',
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
            'parentId' => $this->parent_id,
            'sprintId' => $this->sprint_id,
            'taskId' => $this->task_id,
            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type,
            'points' => (int) $this->points,
            'priority' => $this->priority,
            'status' => $this->status,
            'rank' => (int) $this->rank,
            'hasChildren' => $this->children()->exists(),
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(BacklogItem::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(BacklogItem::class, 'parent_id');
    }

    /**
     * Get all ancestors (parent, grandparent, etc.)
     */
    public function ancestors(): \Illuminate\Support\Collection
    {
        $ancestors = collect();
        $item = $this;

        while ($item->parent) {
            $ancestors->push($item->parent);
            $item = $item->parent;
            
            // Prevent infinite loop in case of data corruption
            if ($ancestors->count() > 10) {
                \Log::error("Potential circular reference detected for BacklogItem {$this->id}");
                break;
            }
        }

        return $ancestors;
    }

    /**
     * Get all descendants (children, grandchildren, etc.)
     */
    public function descendants(): \Illuminate\Support\Collection
    {
        return $this->children->flatMap(function ($child) {
            return collect([$child])->merge($child->descendants());
        });
    }

    /**
     * Scope: Get only root items (no parent)
     */
    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope: Get items within a specific parent
     */
    public function scopeWithinParent($query, ?int $parentId)
    {
        return $query->where('parent_id', $parentId);
    }

    /**
     * Boot method - Add model event listeners
     */
    protected static function boot()
    {
        parent::boot();

        // Prevent circular references
        static::saving(function ($item) {
            if ($item->parent_id && $item->parent_id === $item->id) {
                throw new \RuntimeException('Cannot set item as its own parent');
            }

            if ($item->parent_id) {
                $parent = BacklogItem::find($item->parent_id);
                
                if ($parent && $parent->ancestors()->contains('id', $item->id)) {
                    throw new \RuntimeException('Circular parent reference detected');
                }

                // Validate parent belongs to same project
                if ($parent && $parent->project_id !== $item->project_id) {
                    throw new \RuntimeException('Parent must belong to the same project');
                }
            }
        });
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function sprint(): BelongsTo
    {
        return $this->belongsTo(Sprint::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }
}
