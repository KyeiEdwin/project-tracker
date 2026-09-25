<?php

namespace App\Console\Commands;

use App\Services\DashboardMetricsService;
use App\Models\DashboardMetricCache;
use Illuminate\Console\Command;

class RefreshDashboardMetrics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dashboard:refresh-metrics 
                            {--force : Force refresh even if cache is still valid}
                            {--clear : Clear cache without recalculating}
                            {--metric= : Refresh only a specific metric}
                            {--cleanup : Clean up expired cache entries only}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh dashboard metrics cache';

    /**
     * Execute the console command.
     */
    public function handle(DashboardMetricsService $service): int
    {
        $startTime = microtime(true);

        // Handle cleanup option
        if ($this->option('cleanup')) {
            return $this->handleCleanup();
        }

        // Handle clear option
        if ($this->option('clear')) {
            return $this->handleClear($service);
        }

        // Handle specific metric refresh
        if ($metric = $this->option('metric')) {
            return $this->handleSpecificMetric($service, $metric);
        }

        // Handle full refresh
        return $this->handleFullRefresh($service, $startTime);
    }

    /**
     * Clean up expired cache entries
     */
    protected function handleCleanup(): int
    {
        $this->info('Cleaning up expired cache entries...');
        
        $deleted = DashboardMetricCache::cleanupExpired();
        
        $this->info("✓ Cleaned up {$deleted} expired cache entries.");
        
        return Command::SUCCESS;
    }

    /**
     * Clear all cache
     */
    protected function handleClear(DashboardMetricsService $service): int
    {
        $this->info('Clearing all dashboard metrics cache...');
        
        $service->clearCache();
        
        $this->info('✓ All dashboard metrics cache cleared successfully!');
        
        return Command::SUCCESS;
    }

    /**
     * Refresh a specific metric
     */
    protected function handleSpecificMetric(DashboardMetricsService $service, string $metric): int
    {
        $this->info("Refreshing metric: {$metric}...");
        
        $validMetrics = [
            'total_projects',
            'active_tasks',
            'team_members',
            'completion_rate',
            'pending_reviews',
            'overdue_tasks',
            'milestones',
            'budget_used',
        ];

        if (!in_array($metric, $validMetrics)) {
            $this->error("Invalid metric key: {$metric}");
            $this->line('Valid metrics: ' . implode(', ', $validMetrics));
            return Command::FAILURE;
        }

        // Invalidate the specific metric
        $service->invalidateMetric($metric);

        // Get fresh value
        $methodMap = [
            'total_projects' => 'getTotalProjects',
            'active_tasks' => 'getActiveTasks',
            'team_members' => 'getTeamMembers',
            'completion_rate' => 'getCompletionRate',
            'pending_reviews' => 'getPendingReviews',
            'overdue_tasks' => 'getOverdueTasks',
            'milestones' => 'getMilestones',
            'budget_used' => 'getBudgetUsed',
        ];

        $result = $service->{$methodMap[$metric]}();

        $this->info("✓ Metric '{$metric}' refreshed successfully!");
        $this->line("  Value: {$result['value']}");
        $this->line("  Trend: {$result['trend']['formatted']}");

        return Command::SUCCESS;
    }

    /**
     * Handle full metrics refresh
     */
    protected function handleFullRefresh(DashboardMetricsService $service, float $startTime): int
    {
        $this->info('Refreshing dashboard metrics...');
        $this->newLine();

        if ($this->option('force')) {
            $this->line('→ Force refresh enabled, clearing cache first...');
            $service->clearCache();
        }

        // Get all metrics
        $metrics = $service->getAllMetrics();

        $this->newLine();
        $this->info('✓ Metrics refreshed successfully!');
        $this->newLine();

        // Display results in a table
        $tableData = [];
        foreach ($metrics as $key => $data) {
            // Skip non-metric data (like recentProjects and topPerformers arrays)
            if (!isset($data['value'])) {
                continue;
            }
            
            $tableData[] = [
                $this->formatMetricName($key),
                $data['value'],
                $data['trend']['formatted'] ?? 'N/A',
                $data['cached'] ? '✓ Cached' : '✗ Fresh',
            ];
        }

        $this->table(
            ['Metric', 'Value', 'Trend', 'Status'],
            $tableData
        );
        
        // Show additional data sections
        if (isset($metrics['recentProjects']) && is_array($metrics['recentProjects'])) {
            $this->newLine();
            $this->line("Recent Projects: " . count($metrics['recentProjects']) . " loaded");
        }
        
        if (isset($metrics['topPerformers']) && is_array($metrics['topPerformers'])) {
            $this->line("Top Performers: " . count($metrics['topPerformers']) . " loaded");
        }

        // Show cache stats
        $stats = $service->getCacheStats();
        $this->newLine();
        $this->line("Cache Statistics:");
        $this->line("  Total entries: {$stats['total']}");
        $this->line("  Valid entries: {$stats['valid']}");
        $this->line("  Expired entries: {$stats['expired']}");
        $this->line("  Hit rate: {$stats['hitRate']}%");

        // Show execution time
        $executionTime = round((microtime(true) - $startTime) * 1000, 2);
        $this->newLine();
        $this->line("Execution time: {$executionTime}ms");

        return Command::SUCCESS;
    }

    /**
     * Format metric name for display
     */
    protected function formatMetricName(string $key): string
    {
        return str_replace('_', ' ', ucwords($key, '_'));
    }
}
