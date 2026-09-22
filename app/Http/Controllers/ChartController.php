<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreChartRequest;
use App\Http\Requests\UpdateChartRequest;
use App\Models\Chart;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ChartController extends Controller
{
    public function index(): Response
    {
        $page = $this->inertiaPage(
            Chart::query()->with(['project', 'series'])->latest(),
            fn (Chart $chart) => $chart->toInertia()
        );

        return Inertia::render('Charts/Index', [
            'charts' => $page['data'],
            'pagination' => $page['pagination'],
            'projects' => $this->projectOptions(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Charts/Index', [
            'charts' => [],
            'projects' => $this->projectOptions(),
            'formMode' => 'create',
        ]);
    }

    public function store(StoreChartRequest $request): RedirectResponse
    {
        $chart = Chart::query()->create($request->safe()->except('series'));
        $this->syncSeries($chart, $request->input('series', []));

        return redirect()->route('charts.index')->with('success', 'Chart created.');
    }

    public function show(Chart $chart): Response
    {
        $chart->load(['project', 'series']);

        return Inertia::render('Charts/Index', [
            'charts' => [$chart->toInertia()],
            'chart' => $chart->toInertia(),
            'projects' => $this->projectOptions(),
        ]);
    }

    public function edit(Chart $chart): Response
    {
        $chart->load(['project', 'series']);

        return Inertia::render('Charts/Index', [
            'charts' => [$chart->toInertia()],
            'chart' => $chart->toInertia(),
            'projects' => $this->projectOptions(),
            'formMode' => 'edit',
        ]);
    }

    public function update(UpdateChartRequest $request, Chart $chart): RedirectResponse
    {
        $chart->update($request->safe()->except('series'));

        if ($request->has('series')) {
            $chart->series()->delete();
            $this->syncSeries($chart, $request->input('series', []));
        }

        return redirect()->route('charts.index')->with('success', 'Chart updated.');
    }

    public function destroy(Chart $chart): RedirectResponse
    {
        $chart->series()->delete();
        $chart->delete();

        return redirect()->route('charts.index')->with('success', 'Chart removed.');
    }

    /**
     * @param  array<int, array<string, mixed>>  $series
     */
    private function syncSeries(Chart $chart, array $series): void
    {
        foreach ($series as $index => $row) {
            $chart->series()->create([
                'name' => $row['name'],
                'color' => $row['color'] ?? null,
                'data' => $row['data'] ?? [],
                'sort_order' => $index,
            ]);
        }
    }
}
