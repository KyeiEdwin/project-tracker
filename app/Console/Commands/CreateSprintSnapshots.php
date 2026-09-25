<?php

namespace App\Console\Commands;

use App\Models\Sprint;
use App\Services\SprintService;
use Illuminate\Console\Command;

class CreateSprintSnapshots extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sprints:create-snapshots 
                            {--sprint-id= : Create snapshot for a specific sprint}
                            {--backfill-days= : Backfill snapshots for the last N days}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create daily snapshots for active sprints to track burndown data';

    /**
     * Execute the console command.
     */
    public function handle(SprintService $sprintService): int
    {
        $sprintId = $this->option('sprint-id');
        $backfillDays = $this->option('backfill-days');

        if ($sprintId) {
            return $this->createSnapshotForSprint($sprintId, $sprintService, $backfillDays);
        }

        return $this->createSnapshotsForActiveSprints($sprintService);
    }

    /**
     * Create snapshot for a specific sprint
     */
    protected function createSnapshotForSprint(int $sprintId, SprintService $sprintService, ?int $backfillDays): int
    {
        $sprint = Sprint::find($sprintId);

        if (!$sprint) {
            $this->error("Sprint with ID {$sprintId} not found.");
            return Command::FAILURE;
        }

        try {
            if ($backfillDays) {
                $startDate = now()->subDays($backfillDays)->startOfDay();
                $endDate = now()->startOfDay();
                
                $this->info("Backfilling snapshots for sprint '{$sprint->name}' from {$startDate->toDateString()} to {$endDate->toDateString()}...");
                
                $snapshots = $sprintService->createSnapshotsForDateRange($sprint, $startDate, $endDate);
                $this->info("Created {$snapshots->count()} snapshots for sprint '{$sprint->name}'.");
            } else {
                $snapshot = $sprintService->createDailySnapshot($sprint);
                $this->info("Created snapshot for sprint '{$sprint->name}' on {$snapshot->snapshot_date->toDateString()}.");
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Failed to create snapshot(s) for sprint {$sprintId}: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }

    /**
     * Create snapshots for all active sprints
     */
    protected function createSnapshotsForActiveSprints(SprintService $sprintService): int
    {
        $activeSprints = Sprint::where('status', 'active')->get();

        if ($activeSprints->isEmpty()) {
            $this->info('No active sprints found.');
            return Command::SUCCESS;
        }

        $this->info("Found {$activeSprints->count()} active sprint(s). Creating snapshots...");

        $created = 0;
        $failed = 0;

        foreach ($activeSprints as $sprint) {
            try {
                $snapshot = $sprintService->createDailySnapshot($sprint);
                $created++;
                $this->line("✓ Created snapshot for sprint '{$sprint->name}' (ID: {$sprint->id})");
            } catch (\Exception $e) {
                $failed++;
                $this->error("✗ Failed to create snapshot for sprint '{$sprint->name}' (ID: {$sprint->id}): {$e->getMessage()}");
            }
        }

        $this->newLine();
        $this->info("Summary: {$created} snapshots created, {$failed} failed.");

        return $failed > 0 ? Command::FAILURE : Command::SUCCESS;
    }
}

