<?php

namespace App\Console\Commands;

use App\Models\DashboardMetricCache;
use Illuminate\Console\Command;

class CleanupDashboardCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dashboard:cleanup-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up expired dashboard metrics cache entries';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Cleaning up expired dashboard metrics cache...');

        $before = DashboardMetricCache::count();
        $deleted = DashboardMetricCache::cleanupExpired();
        $after = DashboardMetricCache::count();

        $this->newLine();
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total before', $before],
                ['Deleted', $deleted],
                ['Remaining', $after],
            ]
        );

        if ($deleted > 0) {
            $this->info("✓ Successfully cleaned up {$deleted} expired cache entries!");
        } else {
            $this->line('→ No expired cache entries found.');
        }

        return Command::SUCCESS;
    }
}
