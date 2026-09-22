<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBudgetItemRequest;
use App\Http\Requests\UpdateBudgetItemRequest;
use App\Models\BudgetItem;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class BudgetItemController extends Controller
{
    public function index(): Response
    {
        $page = $this->inertiaPage(
            BudgetItem::query()->with('project')->latest(),
            fn (BudgetItem $item) => $item->toInertia()
        );

        $totals = Project::query()
            ->selectRaw('COALESCE(SUM(budget), 0) as total_budget, COALESCE(SUM(spent), 0) as spent')
            ->first();

        $itemAllocated = BudgetItem::query()->sum('allocated');
        $itemSpent = BudgetItem::query()->sum('spent');

        return Inertia::render('Resources/Budget', [
            'budgetItems' => $page['data'],
            'pagination' => $page['pagination'],
            'budgetData' => [
                'totalBudget' => (float) ($itemAllocated ?: $totals?->total_budget),
                'spent' => (float) ($itemSpent ?: $totals?->spent),
                'remaining' => (float) (($itemAllocated ?: $totals?->total_budget) - ($itemSpent ?: $totals?->spent)),
                'projected' => (float) ($itemSpent ?: $totals?->spent),
            ],
            'projects' => $this->projectOptions(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Resources/Budget', [
            'budgetItems' => [],
            'budgetData' => ['totalBudget' => 0, 'spent' => 0, 'remaining' => 0, 'projected' => 0],
            'projects' => $this->projectOptions(),
            'formMode' => 'create',
        ]);
    }

    public function store(StoreBudgetItemRequest $request): RedirectResponse
    {
        BudgetItem::query()->create($request->validated());

        return redirect()->route('resources.budget')->with('success', 'Budget item added.');
    }

    public function show(BudgetItem $budgetItem): Response
    {
        $budgetItem->load('project');

        return Inertia::render('Resources/Budget', [
            'budgetItems' => [$budgetItem->toInertia()],
            'budgetItem' => $budgetItem->toInertia(),
            'projects' => $this->projectOptions(),
        ]);
    }

    public function edit(BudgetItem $budgetItem): Response
    {
        $budgetItem->load('project');

        return Inertia::render('Resources/Budget', [
            'budgetItems' => [$budgetItem->toInertia()],
            'budgetItem' => $budgetItem->toInertia(),
            'projects' => $this->projectOptions(),
            'formMode' => 'edit',
        ]);
    }

    public function update(UpdateBudgetItemRequest $request, BudgetItem $budgetItem): RedirectResponse
    {
        $budgetItem->update($request->validated());

        return redirect()->route('resources.budget')->with('success', 'Budget item updated.');
    }

    public function destroy(BudgetItem $budgetItem): RedirectResponse
    {
        $budgetItem->delete();

        return redirect()->route('resources.budget')->with('success', 'Budget item removed.');
    }
}
