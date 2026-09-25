<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBacklogItemRequest;
use App\Http\Requests\UpdateBacklogItemRequest;
use App\Models\BacklogItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BacklogItemController extends Controller
{
    public function index(): Response
    {
        $page = $this->inertiaPage(
            BacklogItem::query()->with('project')->orderBy('rank')->orderByDesc('priority'),
            fn (BacklogItem $item) => $item->toInertia()
        );

        return Inertia::render('Agile/Backlog', [
            'backlogItems' => $page['data'],
            'pagination' => $page['pagination'],
            'projects' => $this->projectOptions(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Agile/Backlog', [
            'backlogItems' => [],
            'projects' => $this->projectOptions(),
            'formMode' => 'create',
        ]);
    }

    public function store(StoreBacklogItemRequest $request): RedirectResponse
    {
        BacklogItem::query()->create($request->validated());

        return redirect()->route('agile.backlog')->with('success', 'Backlog item added.');
    }

    public function show(BacklogItem $backlogItem): Response
    {
        $backlogItem->load('project');

        return Inertia::render('Agile/Backlog', [
            'backlogItems' => [$backlogItem->toInertia()],
            'backlogItem' => $backlogItem->toInertia(),
            'projects' => $this->projectOptions(),
        ]);
    }

    public function edit(BacklogItem $backlogItem): Response
    {
        $backlogItem->load('project');

        return Inertia::render('Agile/Backlog', [
            'backlogItems' => [$backlogItem->toInertia()],
            'backlogItem' => $backlogItem->toInertia(),
            'projects' => $this->projectOptions(),
            'formMode' => 'edit',
        ]);
    }

    public function update(UpdateBacklogItemRequest $request, BacklogItem $backlogItem): RedirectResponse
    {
        $backlogItem->update($request->validated());

        return redirect()->route('agile.backlog')->with('success', 'Backlog item updated.');
    }

    public function destroy(BacklogItem $backlogItem): RedirectResponse
    {
        $backlogItem->delete();

        return redirect()->route('agile.backlog')->with('success', 'Backlog item removed.');
    }

    /**
     * Update parent relationship for a backlog item
     */
    public function updateParent(Request $request, BacklogItem $backlogItem): JsonResponse
    {
        $validated = $request->validate([
            'parent_id' => ['nullable', 'integer', 'exists:backlog_items,id'],
        ]);

        try {
            $backlogItem->update(['parent_id' => $validated['parent_id']]);
            
            return response()->json([
                'success' => true,
                'message' => 'Parent updated successfully',
                'backlog_item' => $backlogItem->fresh()->toInertia()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Update rank for a backlog item
     */
    public function updateRank(Request $request, BacklogItem $backlogItem): JsonResponse
    {
        $validated = $request->validate([
            'rank' => ['required', 'integer', 'min:0'],
        ]);

        try {
            $backlogItem->update(['rank' => $validated['rank']]);
            
            return response()->json([
                'success' => true,
                'message' => 'Rank updated successfully',
                'backlog_item' => $backlogItem->fresh()->toInertia()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
