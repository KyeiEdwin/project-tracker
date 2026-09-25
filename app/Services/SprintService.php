<?php

namespace App\Services;

use App\Models\BacklogItem;
use App\Models\Project;
use App\Models\Sprint;
use App\Models\SprintEvent;
use App\Models\SprintSnapshot;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SprintService
{
    /**
     * Calculate planned points for a sprint (sum of all backlog item points)
     */
    public function calculatePlannedPoints(Sprint $sprint): int
    {
        return $sprint->backlogItems()->sum('points') ?? 0;
    }

    /**
     * Calculate completed points for a sprint (sum of done backlog item points)
     */
    public function calculateCompletedPoints(Sprint $sprint): int
    {
        return $sprint->backlogItems()
            ->where('status', 'done')
            ->sum('points') ?? 0;
    }

    /**
     * Recalculate sprint points (triggers accessor refresh)
     * Note: Points are now computed properties, but we can clear cache if needed
     */
    public function recalculateSprintPoints(Sprint $sprint): void
    {
        // Clear any cached metrics for this sprint
        Cache::forget("sprint_metrics_{$sprint->id}");
        
        // Refresh the model to ensure computed properties are fresh
        $sprint->refresh();
    }

    /**
     * Check if a sprint can be started
     */
    public function canStartSprint(Sprint $sprint): bool
    {
        return empty($this->validateSprintStart($sprint));
    }

    /**
     * Validate if a sprint can be started
     * Returns array of error messages (empty if valid)
     */
    public function validateSprintStart(Sprint $sprint): array
    {
        $errors = [];

        // Must be in planned status
        if ($sprint->status !== 'planned') {
            $errors[] = $sprint->status === 'active' 
                ? 'Sprint is already active.'
                : 'Only planned sprints can be started.';
        }

        // Must have valid dates
        if (!$sprint->start_date || !$sprint->end_date) {
            $errors[] = 'Sprint must have start and end dates.';
        }

        if ($sprint->end_date && $sprint->start_date && $sprint->end_date->lt($sprint->start_date)) {
            $errors[] = 'Sprint end date must be after start date.';
        }

        // Check for another active sprint in the same project
        $activeSprintExists = Sprint::where('project_id', $sprint->project_id)
            ->where('status', 'active')
            ->where('id', '!=', $sprint->id)
            ->exists();

        if ($activeSprintExists) {
            $errors[] = 'Another sprint is already active for this project.';
        }

        // Warn if no backlog items (not an error, just a warning)
        if ($sprint->backlogItems()->count() === 0) {
            $errors[] = 'Warning: Sprint has no backlog items assigned.';
        }

        return $errors;
    }

    /**
     * Start a sprint
     */
    public function startSprint(Sprint $sprint): void
    {
        $errors = $this->validateSprintStart($sprint);
        
        if (!empty($errors)) {
            throw new \RuntimeException('Cannot start sprint: ' . implode(' ', $errors));
        }

        DB::transaction(function () use ($sprint) {
            // Auto-close any expired active sprints for this project
            $this->autoCloseExpiredSprints($sprint->project);

            // Set sprint to active
            $sprint->status = 'active';
            $sprint->save();

            // Log event
            $this->logEvent($sprint, 'sprint_started', [
                'started_at' => now()->toIso8601String(),
                'planned_points' => $this->calculatePlannedPoints($sprint),
                'backlog_item_count' => $sprint->backlogItems()->count(),
            ]);

            // Create initial snapshot
            $this->createDailySnapshot($sprint);

            // Clear project velocity cache
            Cache::forget("project_velocity_{$sprint->project_id}");
        });
    }

    /**
     * Close a sprint
     */
    public function closeSprint(Sprint $sprint): void
    {
        if ($sprint->status !== 'active') {
            throw new \RuntimeException('Only active sprints can be closed.');
        }

        DB::transaction(function () use ($sprint) {
            // Create final snapshot
            $this->createDailySnapshot($sprint);

            // Set sprint to completed
            $sprint->status = 'completed';
            $sprint->save();

            // Log event
            $this->logEvent($sprint, 'sprint_closed', [
                'closed_at' => now()->toIso8601String(),
                'planned_points' => $this->calculatePlannedPoints($sprint),
                'completed_points' => $this->calculateCompletedPoints($sprint),
                'completion_rate' => $this->getCompletionRate($sprint),
                'duration_days' => $sprint->start_date->diffInDays($sprint->end_date) + 1,
            ]);

            // Clear project velocity cache to include this completed sprint
            Cache::forget("project_velocity_{$sprint->project_id}");
        });
    }

    /**
     * Auto-close expired sprints for a project
     */
    public function autoCloseExpiredSprints(Project $project): int
    {
        $expiredSprints = Sprint::where('project_id', $project->id)
            ->where('status', 'active')
            ->where('end_date', '<', now()->startOfDay())
            ->get();

        $closedCount = 0;

        foreach ($expiredSprints as $sprint) {
            try {
                $this->closeSprint($sprint);
                $closedCount++;
                
                \Log::info("Auto-closed expired sprint: {$sprint->name} (ID: {$sprint->id})");
            } catch (\Exception $e) {
                \Log::error("Failed to auto-close sprint {$sprint->id}: {$e->getMessage()}");
            }
        }

        return $closedCount;
    }

    /**
     * Add backlog items to a sprint
     */
    public function addBacklogItems(Sprint $sprint, array $itemIds): void
    {
        if ($sprint->status === 'completed') {
            throw new \RuntimeException('Cannot modify a completed sprint.');
        }

        DB::transaction(function () use ($sprint, $itemIds) {
            foreach ($itemIds as $itemId) {
                $item = BacklogItem::find($itemId);
                
                if (!$item) {
                    continue;
                }

                // Validate same project
                if ($item->project_id !== $sprint->project_id) {
                    throw new \RuntimeException("Backlog item {$item->id} belongs to a different project.");
                }

                // Assign to sprint
                $item->sprint_id = $sprint->id;
                $item->save();

                // Log event
                $this->logEvent($sprint, 'item_added', [
                    'backlog_item_id' => $item->id,
                    'title' => $item->title,
                    'type' => $item->type,
                    'points' => $item->points,
                ]);
            }

            // Recalculate points
            $this->recalculateSprintPoints($sprint);
        });
    }

    /**
     * Remove a backlog item from a sprint
     */
    public function removeBacklogItem(Sprint $sprint, BacklogItem $item): void
    {
        if ($sprint->status === 'completed') {
            throw new \RuntimeException('Cannot modify a completed sprint.');
        }

        if ($item->sprint_id !== $sprint->id) {
            throw new \RuntimeException('Item does not belong to this sprint.');
        }

        DB::transaction(function () use ($sprint, $item) {
            $item->sprint_id = null;
            $item->save();

            // Log event
            $this->logEvent($sprint, 'item_removed', [
                'backlog_item_id' => $item->id,
                'title' => $item->title,
                'points' => $item->points,
            ]);

            // Recalculate points
            $this->recalculateSprintPoints($sprint);
        });
    }

    /**
     * Get velocity for a project (average completed points from recent sprints)
     */
    public function getVelocity(Project $project, int $sprintCount = 3): array
    {
        $cacheKey = "project_velocity_{$project->id}_{$sprintCount}";

        return Cache::remember($cacheKey, 3600, function () use ($project, $sprintCount) {
            $completedSprints = Sprint::where('project_id', $project->id)
                ->where('status', 'completed')
                ->orderBy('end_date', 'desc')
                ->limit($sprintCount)
                ->get();

            if ($completedSprints->isEmpty()) {
                return [
                    'average_velocity' => 0,
                    'sprint_count' => 0,
                    'sprints' => [],
                    'trend' => 'unknown',
                ];
            }

            $sprintData = $completedSprints->map(function ($sprint) {
                $planned = $this->calculatePlannedPoints($sprint);
                $completed = $this->calculateCompletedPoints($sprint);
                
                return [
                    'id' => $sprint->id,
                    'name' => $sprint->name,
                    'completed_points' => $completed,
                    'planned_points' => $planned,
                    'completion_rate' => $planned > 0 ? round(($completed / $planned) * 100, 1) : 0,
                    'end_date' => $sprint->end_date->toDateString(),
                ];
            });

            $totalCompleted = $sprintData->sum('completed_points');
            $averageVelocity = round($totalCompleted / $completedSprints->count(), 1);

            // Calculate trend (comparing recent half vs older half)
            $trend = 'stable';
            if ($completedSprints->count() >= 4) {
                $halfPoint = (int) ceil($completedSprints->count() / 2);
                $recentAvg = $sprintData->take($halfPoint)->avg('completed_points');
                $olderAvg = $sprintData->skip($halfPoint)->avg('completed_points');
                
                if ($recentAvg > $olderAvg * 1.1) {
                    $trend = 'improving';
                } elseif ($recentAvg < $olderAvg * 0.9) {
                    $trend = 'declining';
                }
            }

            return [
                'average_velocity' => $averageVelocity,
                'sprint_count' => $completedSprints->count(),
                'sprints' => $sprintData->values()->toArray(),
                'trend' => $trend,
            ];
        });
    }

    /**
     * Get burndown chart data for a sprint
     */
    public function getBurndownData(Sprint $sprint): array
    {
        // Cache burndown data for completed sprints indefinitely
        $cacheKey = "sprint_burndown_{$sprint->id}";
        $cacheTtl = $sprint->status === 'completed' ? null : 3600; // 1 hour for active sprints

        return Cache::remember($cacheKey, $cacheTtl, function () use ($sprint) {
            $startDate = $sprint->start_date;
            $endDate = $sprint->end_date;
            $plannedPoints = $this->calculatePlannedPoints($sprint);

            // Generate date range
            $dates = collect();
            $currentDate = $startDate->copy();
            
            while ($currentDate->lte($endDate)) {
                $dates->push($currentDate->copy());
                $currentDate->addDay();
            }

            // Calculate ideal burndown (linear)
            $totalDays = $dates->count();
            $idealBurndown = $dates->map(function ($date, $index) use ($plannedPoints, $totalDays) {
                $remainingDays = $totalDays - $index;
                return round($plannedPoints * ($remainingDays / $totalDays), 1);
            });

            // Get actual burndown from snapshots
            $snapshots = $sprint->snapshots()
                ->whereBetween('snapshot_date', [$startDate, $endDate])
                ->orderBy('snapshot_date')
                ->get()
                ->keyBy(fn($s) => $s->snapshot_date->toDateString());

            $actualBurndown = $dates->map(function ($date) use ($snapshots, $plannedPoints) {
                $dateStr = $date->toDateString();
                
                if (isset($snapshots[$dateStr])) {
                    return (float) $snapshots[$dateStr]->remaining_points;
                }
                
                // For dates without snapshots, try to find the last known value
                $lastSnapshot = $snapshots->filter(fn($s) => $s->snapshot_date->lte($date))
                    ->sortByDesc('snapshot_date')
                    ->first();
                
                return $lastSnapshot ? (float) $lastSnapshot->remaining_points : (float) $plannedPoints;
            });

            return [
                'dates' => $dates->map(fn($d) => $d->toDateString())->values()->toArray(),
                'ideal' => $idealBurndown->values()->toArray(),
                'actual' => $actualBurndown->values()->toArray(),
                'planned_points' => $plannedPoints,
                'current_remaining' => $plannedPoints - $this->calculateCompletedPoints($sprint),
            ];
        });
    }

    /**
     * Create a daily snapshot for a sprint
     */
    public function createDailySnapshot(Sprint $sprint): SprintSnapshot
    {
        $date = now()->startOfDay()->toDateString();
        $plannedPoints = $this->calculatePlannedPoints($sprint);
        $completedPoints = $this->calculateCompletedPoints($sprint);
        $remainingPoints = $plannedPoints - $completedPoints;

        // Check for existing snapshot today
        $existing = SprintSnapshot::where('sprint_id', $sprint->id)
            ->where('snapshot_date', $date)
            ->first();

        // Calculate scope change (difference in planned points from yesterday)
        $scopeChange = 0;
        if ($existing) {
            $scopeChange = $plannedPoints - $existing->planned_points;
        } else {
            $yesterday = now()->subDay()->startOfDay()->toDateString();
            $yesterdaySnapshot = SprintSnapshot::where('sprint_id', $sprint->id)
                ->where('snapshot_date', $yesterday)
                ->first();
            
            if ($yesterdaySnapshot) {
                $scopeChange = $plannedPoints - $yesterdaySnapshot->planned_points;
            }
        }

        $snapshot = SprintSnapshot::updateOrCreate(
            [
                'sprint_id' => $sprint->id,
                'snapshot_date' => $date,
            ],
            [
                'remaining_points' => $remainingPoints,
                'completed_points' => $completedPoints,
                'planned_points' => $plannedPoints,
                'scope_change' => $scopeChange,
            ]
        );

        return $snapshot;
    }

    /**
     * Create snapshots for a date range (backfill historical data)
     */
    public function createSnapshotsForDateRange(Sprint $sprint, Carbon $startDate, Carbon $endDate): Collection
    {
        $snapshots = collect();
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            try {
                $snapshot = $this->createDailySnapshot($sprint);
                $snapshots->push($snapshot);
            } catch (\Exception $e) {
                \Log::error("Failed to create snapshot for sprint {$sprint->id} on {$currentDate}: {$e->getMessage()}");
            }

            $currentDate->addDay();
        }

        return $snapshots;
    }

    /**
     * Log a sprint event
     */
    public function logEvent(Sprint $sprint, string $eventType, array $metadata = []): SprintEvent
    {
        return SprintEvent::create([
            'sprint_id' => $sprint->id,
            'event_type' => $eventType,
            'user_id' => Auth::id(),
            'metadata' => $metadata,
        ]);
    }

    /**
     * Get sprint event history
     */
    public function getSprintHistory(Sprint $sprint): Collection
    {
        return $sprint->events()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get comprehensive sprint metrics
     */
    public function getSprintMetrics(Sprint $sprint): array
    {
        $planned = $this->calculatePlannedPoints($sprint);
        $completed = $this->calculateCompletedPoints($sprint);

        return [
            'planned_points' => $planned,
            'completed_points' => $completed,
            'remaining_points' => $planned - $completed,
            'completion_rate' => $this->getCompletionRate($sprint),
            'backlog_item_count' => $sprint->backlogItems()->count(),
            'completed_item_count' => $sprint->backlogItems()->where('status', 'done')->count(),
            'days_remaining' => $sprint->end_date ? max(0, now()->diffInDays($sprint->end_date, false)) : 0,
            'days_elapsed' => $sprint->start_date ? max(0, $sprint->start_date->diffInDays(now())) : 0,
            'total_days' => $sprint->start_date && $sprint->end_date 
                ? $sprint->start_date->diffInDays($sprint->end_date) + 1 
                : 0,
        ];
    }

    /**
     * Get completion rate percentage
     */
    protected function getCompletionRate(Sprint $sprint): float
    {
        $planned = $this->calculatePlannedPoints($sprint);
        
        if ($planned === 0) {
            return 0;
        }

        $completed = $this->calculateCompletedPoints($sprint);
        
        return round(($completed / $planned) * 100, 1);
    }
}
