<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReportRequest;
use App\Http\Requests\UpdateReportRequest;
use App\Models\BudgetItem;
use App\Models\Project;
use App\Models\Report;
use App\Models\Risk;
use App\Models\Task;
use App\Models\TeamMember;
use App\Models\TimeEntry;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    public function analytics(): Response
    {
        $reports = Report::query()
            ->with('project')
            ->latest()
            ->get()
            ->map(fn (Report $report) => $report->toInertia())
            ->values();

        if ($reports->isEmpty()) {
            $reports = collect($this->catalog())->values();
        }

        return Inertia::render('Reports/Analytics', [
            'reportTypes' => $reports,
            'metrics' => $this->liveMetrics(),
            'projects' => $this->projectOptions(),
        ]);
    }

    public function index(): Response
    {
        return $this->analytics();
    }

    public function create(): Response
    {
        return Inertia::render('Reports/Analytics', [
            'reportTypes' => $this->catalog(),
            'projects' => $this->projectOptions(),
            'formMode' => 'create',
        ]);
    }

    public function store(StoreReportRequest $request): RedirectResponse
    {
        $report = Report::query()->create(array_merge($request->validated(), [
            'generated_at' => now(),
            'metrics' => $request->input('metrics', $this->liveMetrics()),
        ]));

        return redirect()->route('reports.analytics')->with('success', 'Report saved.');
    }

    public function show(Report $report): Response
    {
        $report->load('project');

        return Inertia::render('Reports/Analytics', [
            'reportTypes' => [$report->toInertia()],
            'report' => $report->toInertia(),
            'metrics' => $report->metrics ?? $this->liveMetrics(),
            'projects' => $this->projectOptions(),
        ]);
    }

    public function edit(Report $report): Response
    {
        return $this->show($report)->with('formMode', 'edit');
    }

    public function update(UpdateReportRequest $request, Report $report): RedirectResponse
    {
        $report->update(array_merge($request->validated(), [
            'generated_at' => now(),
        ]));

        return redirect()->route('reports.analytics')->with('success', 'Report updated.');
    }

    public function destroy(Report $report): RedirectResponse
    {
        $report->delete();

        return redirect()->route('reports.analytics')->with('success', 'Report removed.');
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function catalog(): array
    {
        return [
            ['id' => 'status', 'name' => 'Project Status Report', 'description' => 'Overall project health and progress', 'icon' => 'ri-bar-chart-line', 'color' => 'primary', 'type' => 'status'],
            ['id' => 'sprint', 'name' => 'Sprint Report', 'description' => 'Sprint velocity and burndown', 'icon' => 'ri-speed-line', 'color' => 'success', 'type' => 'sprint'],
            ['id' => 'resource', 'name' => 'Resource Utilization', 'description' => 'Team allocation and availability', 'icon' => 'ri-user-line', 'color' => 'info', 'type' => 'resource'],
            ['id' => 'budget', 'name' => 'Budget Report', 'description' => 'Budget vs actual spending', 'icon' => 'ri-money-dollar-circle-line', 'color' => 'warning', 'type' => 'budget'],
            ['id' => 'risk', 'name' => 'Risk Report', 'description' => 'Active risks and mitigation status', 'icon' => 'ri-alert-line', 'color' => 'danger', 'type' => 'risk'],
            ['id' => 'time', 'name' => 'Time Tracking Report', 'description' => 'Hours logged by project/task', 'icon' => 'ri-time-line', 'color' => 'secondary', 'type' => 'time'],
        ];
    }

    /**
     * @return array<string, int|float>
     */
    private function liveMetrics(): array
    {
        return [
            'totalProjects' => Project::query()->count(),
            'activeTasks' => Task::query()->whereNotIn('status', ['completed', 'done'])->count(),
            'teamMembers' => TeamMember::query()->count(),
            'openRisks' => Risk::query()->where('status', '!=', 'closed')->count(),
            'hoursLogged' => (float) TimeEntry::query()->sum('hours'),
            'budgetSpent' => (float) BudgetItem::query()->sum('spent'),
        ];
    }
}
