<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSprintRequest;
use App\Http\Requests\UpdateSprintRequest;
use App\Models\BacklogItem;
use App\Models\Sprint;
use App\Models\Task;
use App\Services\SprintService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SprintController extends Controller
{
    public function __construct(
        protected SprintService $sprintService
    ) {}
    
    public function index(): Response
    {
        $page = $this->inertiaPage(
            Sprint::query()->with('project')->latest('start_date'),
            fn (Sprint $sprint) => $sprint->toInertia()
        );

        $current = Sprint::query()
            ->with('project')
            ->where('status', 'active')
            ->latest('start_date')
            ->first();

        $taskCounts = ['total' => 0, 'done' => 0, 'inProgress' => 0, 'todo' => 0];

        if ($current) {
            $tasks = Task::query()->where('sprint_id', $current->id);
            $taskCounts['total'] = (clone $tasks)->count();
            $taskCounts['done'] = (clone $tasks)->whereIn('status', ['completed', 'done'])->count();
            $taskCounts['inProgress'] = (clone $tasks)->where('status', 'in-progress')->count();
            $taskCounts['todo'] = (clone $tasks)->whereIn('status', ['pending', 'todo', 'backlog'])->count();
        }

        return Inertia::render('Agile/Sprints', [
            'sprints' => $page['data'],
            'pagination' => $page['pagination'],
            'currentSprint' => $current ? array_merge($current->toInertia(), [
                'totalPoints' => (int) $current->planned_points,
                'completedPoints' => (int) $current->completed_points,
                'tasks' => $taskCounts,
            ]) : [
                'name' => 'No active sprint',
                'goal' => 'Create a sprint to start tracking velocity.',
                'daysRemaining' => 0,
                'totalPoints' => 0,
                'completedPoints' => 0,
                'tasks' => $taskCounts,
            ],
            'projects' => $this->projectOptions(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Agile/Sprints', [
            'sprints' => [],
            'currentSprint' => null,
            'projects' => $this->projectOptions(),
            'formMode' => 'create',
        ]);
    }

    public function store(StoreSprintRequest $request): RedirectResponse
    {
        Sprint::query()->create($request->validated());

        return redirect()->route('agile.sprints')->with('success', 'Sprint created.');
    }

    public function show(Sprint $sprint): Response
    {
        $sprint->load('project');

        return Inertia::render('Agile/Sprints', [
            'sprints' => [$sprint->toInertia()],
            'sprint' => $sprint->toInertia(),
            'currentSprint' => $sprint->toInertia(),
            'projects' => $this->projectOptions(),
        ]);
    }

    public function edit(Sprint $sprint): Response
    {
        $sprint->load('project');

        return Inertia::render('Agile/Sprints', [
            'sprints' => [$sprint->toInertia()],
            'sprint' => $sprint->toInertia(),
            'projects' => $this->projectOptions(),
            'formMode' => 'edit',
        ]);
    }

    public function update(UpdateSprintRequest $request, Sprint $sprint): RedirectResponse
    {
        $sprint->update($request->validated());

        return redirect()->route('agile.sprints')->with('success', 'Sprint updated.');
    }

    public function destroy(Sprint $sprint): RedirectResponse
    {
        $sprint->delete();

        return redirect()->route('agile.sprints')->with('success', 'Sprint removed.');
    }

    /**
     * Start a sprint
     */
    public function start(Sprint $sprint): JsonResponse
    {
        try {
            $this->sprintService->startSprint($sprint);
            
            return response()->json([
                'success' => true,
                'message' => 'Sprint started successfully',
                'sprint' => $sprint->fresh()->toInertia()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Close a sprint
     */
    public function close(Sprint $sprint): JsonResponse
    {
        try {
            $this->sprintService->closeSprint($sprint);
            
            return response()->json([
                'success' => true,
                'message' => 'Sprint closed successfully',
                'sprint' => $sprint->fresh()->toInertia()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Add backlog items to a sprint
     */
    public function addBacklogItems(Request $request, Sprint $sprint): JsonResponse
    {
        $validated = $request->validate([
            'backlog_item_ids' => ['required', 'array', 'min:1'],
            'backlog_item_ids.*' => ['required', 'integer', 'exists:backlog_items,id']
        ]);

        try {
            $this->sprintService->addBacklogItems($sprint, $validated['backlog_item_ids']);
            
            return response()->json([
                'success' => true,
                'message' => 'Items added to sprint successfully',
                'sprint' => $sprint->fresh()->toInertia()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Remove a backlog item from a sprint
     */
    public function removeBacklogItem(Sprint $sprint, BacklogItem $backlogItem): JsonResponse
    {
        try {
            $this->sprintService->removeBacklogItem($sprint, $backlogItem);
            
            return response()->json([
                'success' => true,
                'message' => 'Item removed from sprint successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Get burndown chart data for a sprint
     */
    public function burndown(Sprint $sprint): JsonResponse
    {
        try {
            $burndownData = $this->sprintService->getBurndownData($sprint);
            
            return response()->json([
                'success' => true,
                'data' => $burndownData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get velocity metrics for a sprint's project
     */
    public function velocity(Sprint $sprint): JsonResponse
    {
        try {
            $velocityData = $this->sprintService->getVelocity($sprint->project);
            
            return response()->json([
                'success' => true,
                'data' => $velocityData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get sprint event history
     */
    public function events(Sprint $sprint): JsonResponse
    {
        try {
            $events = $this->sprintService->getSprintHistory($sprint);
            
            return response()->json([
                'success' => true,
                'data' => $events->map(fn($event) => $event->toInertia())
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get comprehensive sprint metrics
     */
    public function metrics(Sprint $sprint): JsonResponse
    {
        try {
            $metrics = $this->sprintService->getSprintMetrics($sprint);
            
            return response()->json([
                'success' => true,
                'data' => $metrics
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
