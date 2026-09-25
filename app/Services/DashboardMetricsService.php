<?php

namespace App\Services;

use App\Models\DashboardMetricCache;
use App\Models\Project;
use App\Models\Task;
use App\Models\TeamMember;
use App\Models\Milestone;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardMetricsService
{
    /**
     * Cache TTL in minutes
     */
    protected int $cacheTtl = 15;

    /**
     * Get all dashboard metrics with caching
     */
    public function getAllMetrics(): array
    {
        return [
            'totalProjects' => $this->getTotalProjects(),
            'activeTasks' => $this->getActiveTasks(),
            'teamMembers' => $this->getTeamMembers(),
            'completionRate' => $this->getCompletionRate(),
            'pendingReviews' => $this->getPendingReviews(),
            'overdueTasks' => $this->getOverdueTasks(),
            'milestones' => $this->getMilestones(),
            'budgetUsed' => $this->getBudgetUsed(),
            'recentProjects' => $this->getRecentProjects(),
            'topPerformers' => $this->getTopPerformers(),
        ];
    }

    /**
     * Get or calculate Total Projects metric
     */
    public function getTotalProjects(): array
    {
        return $this->getCachedOrCalculate('total_projects', function () {
            $current = Project::count();
            $lastMonth = Project::where('created_at', '>=', Carbon::now()->subMonth())
                ->count();

            $previous = Project::where('created_at', '<', Carbon::now()->subMonth())
                ->count();

            return [
                'value' => $current,
                'previous' => $previous,
                'metadata' => [
                    'newThisMonth' => $lastMonth,
                    'active' => Project::whereNotIn('status', ['completed', 'archived'])->count(),
                    'completed' => Project::where('status', 'completed')->count(),
                ],
            ];
        });
    }

    /**
     * Get or calculate Active Tasks metric
     */
    public function getActiveTasks(): array
    {
        return $this->getCachedOrCalculate('active_tasks', function () {
            $current = Task::whereNotIn('status', ['completed', 'done', 'cancelled'])
                ->count();

            $previous = Task::whereNotIn('status', ['completed', 'done', 'cancelled'])
                ->where('created_at', '<', Carbon::now()->subWeek())
                ->count();

            return [
                'value' => $current,
                'previous' => $previous,
                'metadata' => [
                    'inProgress' => Task::where('status', 'in_progress')->count(),
                    'pending' => Task::where('status', 'pending')->count(),
                    'blocked' => Task::where('status', 'blocked')->count(),
                ],
            ];
        });
    }

    /**
     * Get or calculate Team Members metric
     */
    public function getTeamMembers(): array
    {
        return $this->getCachedOrCalculate('team_members', function () {
            $current = TeamMember::count();
            $previous = TeamMember::where('created_at', '<', Carbon::now()->subMonth())
                ->count();

            $newThisMonth = TeamMember::where('created_at', '>=', Carbon::now()->subMonth())->count();

            return [
                'value' => $current,
                'previous' => $previous,
                'metadata' => [
                    'newThisMonth' => $newThisMonth,
                    'change' => $current - $previous,
                ],
            ];
        });
    }

    /**
     * Get or calculate Completion Rate metric
     */
    public function getCompletionRate(): array
    {
        return $this->getCachedOrCalculate('completion_rate', function () {
            $totalProjects = Project::count();
            $completedProjects = Project::where('status', 'completed')->count();

            $currentRate = $totalProjects > 0
                ? round(($completedProjects / $totalProjects) * 100, 1)
                : 0;

            // Calculate previous month's rate
            $previousTotal = Project::where('created_at', '<', Carbon::now()->subMonth())
                ->count();
            $previousCompleted = Project::where('status', 'completed')
                ->where('updated_at', '<', Carbon::now()->subMonth())
                ->count();

            $previousRate = $previousTotal > 0
                ? round(($previousCompleted / $previousTotal) * 100, 1)
                : 0;

            return [
                'value' => $currentRate,
                'previous' => $previousRate,
                'metadata' => [
                    'completedProjects' => $completedProjects,
                    'totalProjects' => $totalProjects,
                    'completedTasks' => Task::whereIn('status', ['completed', 'done'])->count(),
                    'totalTasks' => Task::count(),
                ],
            ];
        });
    }

    /**
     * Get or calculate Pending Reviews metric
     */
    public function getPendingReviews(): array
    {
        return $this->getCachedOrCalculate('pending_reviews', function () {
            $current = Task::where('status', 'review')->count();
            
            // Count from last week for comparison
            $previous = Task::where('status', 'review')
                ->where('updated_at', '<', Carbon::now()->subWeek())
                ->count();

            $today = Task::where('status', 'review')
                ->whereDate('updated_at', Carbon::today())
                ->count();

            $critical = Task::where('status', 'review')
                ->where('priority', 'high')
                ->count();

            return [
                'value' => $current,
                'previous' => $previous > 0 ? $previous : $current,
                'metadata' => [
                    'today' => $today,
                    'critical' => $critical,
                ],
            ];
        });
    }

    /**
     * Get or calculate Overdue Tasks metric
     */
    public function getOverdueTasks(): array
    {
        return $this->getCachedOrCalculate('overdue_tasks', function () {
            $current = Task::where('due_date', '<', Carbon::today())
                ->whereNotIn('status', ['completed', 'done', 'cancelled'])
                ->count();

            $previous = Task::where('due_date', '<', Carbon::now()->subWeek())
                ->whereNotIn('status', ['completed', 'done', 'cancelled'])
                ->count();

            $critical = Task::where('due_date', '<', Carbon::today())
                ->whereNotIn('status', ['completed', 'done', 'cancelled'])
                ->where('priority', 'high')
                ->count();

            $overdueByWeek = Task::where('due_date', '<', Carbon::now()->subWeek())
                ->whereNotIn('status', ['completed', 'done', 'cancelled'])
                ->count();

            return [
                'value' => $current,
                'previous' => $previous > 0 ? $previous : $current,
                'metadata' => [
                    'critical' => $critical,
                    'overdueByWeek' => $overdueByWeek,
                ],
            ];
        });
    }

    /**
     * Get or calculate Milestones metric
     */
    public function getMilestones(): array
    {
        return $this->getCachedOrCalculate('milestones', function () {
            $current = Milestone::whereNotIn('status', ['completed', 'archived'])->count();
            $previous = Milestone::whereNotIn('status', ['completed', 'archived'])
                ->where('created_at', '<', Carbon::now()->subMonth())
                ->count();

            $upcoming = Milestone::where('due_date', '>=', Carbon::today())
                ->where('due_date', '<=', Carbon::today()->addDays(30))
                ->whereNotIn('status', ['completed', 'archived'])
                ->count();

            $completed = Milestone::where('status', 'completed')->count();
            $total = Milestone::count();

            return [
                'value' => $current,
                'previous' => $previous > 0 ? $previous : $current,
                'metadata' => [
                    'upcoming' => $upcoming,
                    'completed' => $completed,
                    'total' => $total,
                ],
            ];
        });
    }

    /**
     * Get or calculate Budget Used metric
     */
    public function getBudgetUsed(): array
    {
        return $this->getCachedOrCalculate('budget_used', function () {
            $budgetData = Project::select(
                DB::raw('SUM(budget) as total_budget'),
                DB::raw('SUM(spent) as total_spent')
            )->first();

            $totalBudget = $budgetData->total_budget ?? 0;
            $totalSpent = $budgetData->total_spent ?? 0;

            $currentPercentage = $totalBudget > 0
                ? round(($totalSpent / $totalBudget) * 100, 1)
                : 0;

            // Previous month's budget usage
            $previousData = Project::where('updated_at', '<', Carbon::now()->subMonth())
                ->select(
                    DB::raw('SUM(budget) as total_budget'),
                    DB::raw('SUM(spent) as total_spent')
                )->first();

            $previousBudget = $previousData->total_budget ?? 0;
            $previousSpent = $previousData->total_spent ?? 0;
            
            $previousPercentage = $previousBudget > 0
                ? round(($previousSpent / $previousBudget) * 100, 1)
                : 0;

            return [
                'value' => $currentPercentage,
                'previous' => $previousPercentage > 0 ? $previousPercentage : $currentPercentage,
                'metadata' => [
                    'totalBudget' => $totalBudget,
                    'totalSpent' => $totalSpent,
                    'remaining' => max(0, $totalBudget - $totalSpent),
                    'currencyFormatted' => [
                        'budget' => '$' . number_format($totalBudget, 0),
                        'spent' => '$' . number_format($totalSpent, 0),
                        'remaining' => '$' . number_format(max(0, $totalBudget - $totalSpent), 0),
                    ],
                ],
            ];
        });
    }

    /**
     * Generic cache getter/calculator
     */
    protected function getCachedOrCalculate(string $metricKey, callable $calculator): array
    {
        // Try to get valid cached metric
        $cached = DashboardMetricCache::where('metric_key', $metricKey)
            ->valid()
            ->first();

        if ($cached) {
            return [
                'value' => is_numeric($cached->metric_value) 
                    ? (strpos($cached->metric_value, '.') !== false 
                        ? (float) $cached->metric_value 
                        : (int) $cached->metric_value)
                    : $cached->metric_value,
                'trend' => [
                    'direction' => $cached->trend_direction,
                    'percentage' => (float) $cached->trend_percentage,
                    'formatted' => $this->formatTrend($cached->trend_direction, (float) $cached->trend_percentage),
                ],
                'metadata' => $cached->metadata ?? [],
                'cached' => true,
                'calculatedAt' => $cached->calculated_at->toIso8601String(),
            ];
        }

        // Calculate fresh metric
        $result = $calculator();
        
        // Calculate trend
        $trend = $this->calculateTrend(
            $result['value'],
            $result['previous'] ?? $result['value']
        );

        // Store in cache
        DashboardMetricCache::setMetric(
            $metricKey,
            $result['value'],
            $result['previous'] ?? $result['value'],
            $trend['direction'],
            $trend['percentage'],
            $result['metadata'] ?? [],
            $this->cacheTtl
        );

        return [
            'value' => $result['value'],
            'trend' => $trend,
            'metadata' => $result['metadata'] ?? [],
            'cached' => false,
            'calculatedAt' => Carbon::now()->toIso8601String(),
        ];
    }

    /**
     * Calculate trend direction and percentage
     */
    protected function calculateTrend($current, $previous): array
    {
        if ($previous == 0 || $previous == $current) {
            return [
                'direction' => 'stable',
                'percentage' => 0.0,
                'formatted' => '0%',
            ];
        }

        $change = $current - $previous;
        $percentage = round(($change / abs($previous)) * 100, 1);

        $direction = 'stable';
        if ($percentage > 0.5) {
            $direction = 'up';
        } elseif ($percentage < -0.5) {
            $direction = 'down';
        }

        return [
            'direction' => $direction,
            'percentage' => abs($percentage),
            'formatted' => $this->formatTrend($direction, abs($percentage)),
        ];
    }

    /**
     * Format trend for display
     */
    protected function formatTrend(string $direction, float $percentage): string
    {
        $sign = $direction === 'up' ? '+' : ($direction === 'down' ? '-' : '');
        return $sign . $percentage . '%';
    }

    /**
     * Clear all cached metrics (useful for testing or manual refresh)
     */
    public function clearCache(): void
    {
        DashboardMetricCache::clearAll();
    }

    /**
     * Invalidate specific metric
     */
    public function invalidateMetric(string $metricKey): void
    {
        DashboardMetricCache::clearMetric($metricKey);
    }

    /**
     * Invalidate multiple metrics
     */
    public function invalidateMetrics(array $metricKeys): void
    {
        foreach ($metricKeys as $key) {
            $this->invalidateMetric($key);
        }
    }

    /**
     * Get cache statistics
     */
    public function getCacheStats(): array
    {
        $total = DashboardMetricCache::count();
        $valid = DashboardMetricCache::valid()->count();
        $expired = DashboardMetricCache::expired()->count();

        return [
            'total' => $total,
            'valid' => $valid,
            'expired' => $expired,
            'hitRate' => $total > 0 ? round(($valid / $total) * 100, 2) : 0,
        ];
    }

    /**
     * Warm up cache by calculating all metrics
     */
    public function warmCache(): array
    {
        $this->clearCache();
        return $this->getAllMetrics();
    }

    /**
     * Get recent projects with activity
     */
    public function getRecentProjects(int $limit = 4): array
    {
        $projects = Project::with('teamMembers')
            ->select(['id', 'name', 'status', 'progress', 'updated_at'])
            ->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($project) {
                return [
                    'id' => $project->id,
                    'name' => $project->name,
                    'status' => ucfirst($project->status),
                    'progress' => (int) $project->progress,
                    'team' => $project->teamMembers()->count(),
                    'color' => $this->getStatusColor($project->status),
                    'updatedAt' => $project->updated_at->diffForHumans(),
                ];
            })
            ->toArray();

        return $projects;
    }

    /**
     * Get top performing team members by completed tasks
     */
    public function getTopPerformers(int $limit = 5): array
    {
        $performers = TeamMember::select(['id', 'name', 'email', 'role'])
            ->withCount([
                'tasks as completed_tasks' => function ($query) {
                    $query->whereIn('status', ['completed', 'done'])
                        ->whereMonth('updated_at', Carbon::now()->month);
                }
            ])
            ->orderBy('completed_tasks', 'desc')
            ->limit($limit)
            ->get()
            ->filter(function ($member) {
                return $member->completed_tasks > 0;
            })
            ->map(function ($member, $index) {
                return [
                    'id' => $member->id,
                    'name' => $member->name,
                    'email' => $member->email,
                    'role' => $member->role ?? 'Team Member',
                    'tasks' => $member->completed_tasks,
                    'rank' => $index + 1,
                    'avatar' => $this->getAvatarUrl($member),
                ];
            })
            ->values()
            ->toArray();

        return $performers;
    }

    /**
     * Get status color for badge
     */
    protected function getStatusColor(string $status): string
    {
        return match (strtolower($status)) {
            'completed', 'done' => 'green',
            'in_progress', 'in progress', 'active' => 'blue',
            'planning', 'pending' => 'purple',
            'review', 'testing' => 'orange',
            'on_hold', 'blocked' => 'yellow',
            'cancelled', 'archived' => 'gray',
            default => 'blue',
        };
    }

    /**
     * Get avatar URL for team member
     */
    protected function getAvatarUrl(TeamMember $member): string
    {
        // If you have avatar field in database, use it
        // Otherwise generate initial-based avatar or use default
        if (!empty($member->avatar)) {
            return $member->avatar;
        }

        // Generate avatar URL based on initials or use service like UI Avatars
        $name = urlencode($member->name);
        return "https://ui-avatars.com/api/?name={$name}&background=random&size=128";
    }
}
