<?php

namespace App\Observers;

use App\Models\BacklogItem;
use App\Models\Sprint;
use App\Services\SprintService;

class BacklogItemObserver
{
    public function __construct(
        protected SprintService $sprintService
    ) {}

    /**
     * Handle the BacklogItem "created" event.
     */
    public function created(BacklogItem $backlogItem): void
    {
        if ($backlogItem->sprint_id) {
            $sprint = $backlogItem->sprint;
            
            if ($sprint) {
                $this->sprintService->recalculateSprintPoints($sprint);
                $this->sprintService->logEvent(
                    $sprint,
                    'item_added',
                    [
                        'backlog_item_id' => $backlogItem->id,
                        'title' => $backlogItem->title,
                        'type' => $backlogItem->type,
                        'points' => $backlogItem->points,
                    ]
                );
            }
        }
    }

    /**
     * Handle the BacklogItem "updated" event.
     */
    public function updated(BacklogItem $backlogItem): void
    {
        $sprintsToUpdate = [];

        // If sprint changed, update both old and new sprint
        if ($backlogItem->wasChanged('sprint_id')) {
            $oldSprintId = $backlogItem->getOriginal('sprint_id');
            $newSprintId = $backlogItem->sprint_id;

            if ($oldSprintId) {
                $oldSprint = Sprint::find($oldSprintId);
                if ($oldSprint) {
                    $sprintsToUpdate[] = $oldSprint;
                    $this->sprintService->logEvent(
                        $oldSprint,
                        'item_removed',
                        [
                            'backlog_item_id' => $backlogItem->id,
                            'title' => $backlogItem->title,
                            'points' => $backlogItem->points,
                            'moved_to_sprint_id' => $newSprintId,
                        ]
                    );
                }
            }

            if ($newSprintId) {
                $newSprint = Sprint::find($newSprintId);
                if ($newSprint) {
                    $sprintsToUpdate[] = $newSprint;
                    $this->sprintService->logEvent(
                        $newSprint,
                        'item_added',
                        [
                            'backlog_item_id' => $backlogItem->id,
                            'title' => $backlogItem->title,
                            'type' => $backlogItem->type,
                            'points' => $backlogItem->points,
                            'moved_from_sprint_id' => $oldSprintId,
                        ]
                    );
                }
            }
        }
        // If points or status changed (and item is in a sprint), update current sprint
        elseif ($backlogItem->wasChanged(['points', 'status']) && $backlogItem->sprint_id) {
            $sprint = $backlogItem->sprint;
            if ($sprint) {
                $sprintsToUpdate[] = $sprint;

                // Log estimate change
                if ($backlogItem->wasChanged('points')) {
                    $this->sprintService->logEvent(
                        $sprint,
                        'estimate_changed',
                        [
                            'backlog_item_id' => $backlogItem->id,
                            'title' => $backlogItem->title,
                            'old_points' => $backlogItem->getOriginal('points'),
                            'new_points' => $backlogItem->points,
                        ]
                    );
                }

                // Log status change
                if ($backlogItem->wasChanged('status')) {
                    $this->sprintService->logEvent(
                        $sprint,
                        'status_updated',
                        [
                            'backlog_item_id' => $backlogItem->id,
                            'title' => $backlogItem->title,
                            'old_status' => $backlogItem->getOriginal('status'),
                            'new_status' => $backlogItem->status,
                        ]
                    );
                }
            }
        }

        // Recalculate points for all affected sprints
        foreach (array_unique($sprintsToUpdate) as $sprint) {
            if ($sprint) {
                try {
                    $this->sprintService->recalculateSprintPoints($sprint);
                } catch (\Exception $e) {
                    \Log::error("Failed to recalculate sprint points for sprint {$sprint->id}: {$e->getMessage()}");
                }
            }
        }
    }

    /**
     * Handle the BacklogItem "deleted" event.
     */
    public function deleted(BacklogItem $backlogItem): void
    {
        if ($backlogItem->sprint_id) {
            $sprint = Sprint::find($backlogItem->sprint_id);
            
            if ($sprint) {
                $this->sprintService->recalculateSprintPoints($sprint);
                $this->sprintService->logEvent(
                    $sprint,
                    'item_removed',
                    [
                        'backlog_item_id' => $backlogItem->id,
                        'title' => $backlogItem->title,
                        'points' => $backlogItem->points,
                        'reason' => 'deleted',
                    ]
                );
            }
        }
    }

    /**
     * Handle the BacklogItem "restored" event.
     */
    public function restored(BacklogItem $backlogItem): void
    {
        if ($backlogItem->sprint_id) {
            $sprint = $backlogItem->sprint;
            
            if ($sprint) {
                $this->sprintService->recalculateSprintPoints($sprint);
                $this->sprintService->logEvent(
                    $sprint,
                    'item_added',
                    [
                        'backlog_item_id' => $backlogItem->id,
                        'title' => $backlogItem->title,
                        'points' => $backlogItem->points,
                        'reason' => 'restored',
                    ]
                );
            }
        }
    }
}
