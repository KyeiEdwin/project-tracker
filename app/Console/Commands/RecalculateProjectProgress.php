<?php

namespace App\Console\Commands;

use App\Services\ProjectProgressService;
use App\Models\Project;
use Illuminate\Console\Command;

class RecalculateProjectProgress extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'projects:recalculate-progress 
                            {--project= : Specific project ID}
                            {--all : Recalculate all projects}
                            {--dry-run : Show what would be updated without saving}
                            {--show-breakdown : Display detailed breakdown}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate project progress based on task completion';

    /**
     * Execute the console command.
     */
    public function handle(ProjectProgressService $service): int
    {
        $dryRun = $this->option('dry-run');
        $showBreakdown = $this->option('show-breakdown');

        if ($projectId = $this->option('project')) {
            return $this->recalculateOne($service, $projectId, $dryRun, $showBreakdown);
        }

        if ($this->option('all')) {
            return $this->recalculateAll($service, $dryRun);
        }

        $this->error('Please specify --project=ID or --all');
        $this->line('');
        $this->line('Examples:');
        $this->line('  php artisan projects:recalculate-progress --project=1');
        $this->line('  php artisan projects:recalculate-progress --all');
        $this->line('  php artisan projects:recalculate-progress --all --dry-run');
        
        return Command::FAILURE;
    }

    /**
     * Recalculate progress for a single project
     */
    protected function recalculateOne(ProjectProgressService $service, int $projectId, bool $dryRun, bool $showBreakdown): int
    {
        $project = Project::find($projectId);

        if (!$project) {
            $this->error("Project #{$projectId} not found");
            return Command::FAILURE;
        }

        $this->info("Recalculating progress for project: {$project->name}");
        $this->newLine();

        $oldProgress = $project->progress;
        $newProgress = $service->calculateProgress($project, !$dryRun);
        $change = $newProgress - $oldProgress;

        // Display results
        $this->table(
            ['Metric', 'Value'],
            [
                ['Project ID', $project->id],
                ['Project Name', $project->name],
                ['Project Type', ucfirst($project->project_type ?? 'default')],
                ['Old Progress', $oldProgress . '%'],
                ['New Progress', $newProgress . '%'],
                ['Change', ($change > 0 ? '+' : '') . $change . '%'],
            ]
        );

        if ($dryRun) {
            $this->warn('  [DRY RUN - Changes not saved]');
        } else {
            $this->info('  ✓ Progress updated successfully!');
        }

        // Show detailed breakdown if requested
        if ($showBreakdown) {
            $this->newLine();
            $this->showBreakdown($service, $project);
        }

        return Command::SUCCESS;
    }

    /**
     * Recalculate progress for all projects
     */
    protected function recalculateAll(ProjectProgressService $service, bool $dryRun): int
    {
        $this->info('Recalculating progress for all projects...');
        $this->newLine();

        $projects = Project::all();
        
        if ($projects->isEmpty()) {
            $this->warn('No projects found in database.');
            return Command::SUCCESS;
        }

        $bar = $this->output->createProgressBar($projects->count());
        $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %message%');

        $changes = [];
        $errors = [];

        foreach ($projects as $project) {
            $bar->setMessage("Processing: {$project->name}");
            
            try {
                $oldProgress = $project->progress;
                $newProgress = $service->calculateProgress($project, !$dryRun);

                if ($oldProgress != $newProgress) {
                    $changes[] = [
                        'id' => $project->id,
                        'project' => $project->name,
                        'old' => $oldProgress,
                        'new' => $newProgress,
                        'delta' => $newProgress - $oldProgress,
                    ];
                }
            } catch (\Exception $e) {
                $errors[] = [
                    'project' => $project->name,
                    'error' => $e->getMessage(),
                ];
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Display results
        if (empty($changes) && empty($errors)) {
            $this->info('✓ No changes needed - all projects are up to date!');
            return Command::SUCCESS;
        }

        if (!empty($changes)) {
            $this->info('Changes detected:');
            $this->table(
                ['ID', 'Project', 'Old %', 'New %', 'Change'],
                array_map(fn($c) => [
                    $c['id'],
                    $c['project'],
                    $c['old'] . '%',
                    $c['new'] . '%',
                    ($c['delta'] > 0 ? '+' : '') . $c['delta'] . '%'
                ], $changes)
            );

            if ($dryRun) {
                $this->warn('[DRY RUN - No changes saved]');
            } else {
                $this->info('✓ Progress updated for ' . count($changes) . ' projects');
            }
        }

        if (!empty($errors)) {
            $this->newLine();
            $this->error('Errors encountered:');
            $this->table(
                ['Project', 'Error'],
                $errors
            );
        }

        return Command::SUCCESS;
    }

    /**
     * Display detailed breakdown for a project
     */
    protected function showBreakdown(ProjectProgressService $service, Project $project): void
    {
        $breakdown = $service->getProgressBreakdown($project);

        $this->line('Detailed Breakdown:');
        $this->newLine();

        // Task Summary
        $this->line('<fg=cyan>Task Summary:</>');
        $this->table(
            ['Status', 'Count'],
            [
                ['Total Tasks', $breakdown['total']],
                ['✓ Completed', $breakdown['completed']],
                ['⚡ In Progress', $breakdown['inProgress']],
                ['○ Not Started', $breakdown['notStarted']],
            ]
        );

        // Estimated Hours
        if ($breakdown['totalEstimatedHours'] > 0) {
            $this->newLine();
            $this->line('<fg=cyan>Estimated Hours:</>');
            $this->table(
                ['Metric', 'Hours'],
                [
                    ['Total Estimated', number_format($breakdown['totalEstimatedHours'], 1)],
                    ['Completed', number_format($breakdown['completedEstimatedHours'], 1)],
                    ['Remaining', number_format($breakdown['remainingEstimatedHours'], 1)],
                ]
            );
        }

        // Status Distribution
        if (!empty($breakdown['breakdown']['byStatus'])) {
            $this->newLine();
            $this->line('<fg=cyan>Tasks by Status:</>');
            $statusData = [];
            foreach ($breakdown['breakdown']['byStatus'] as $status => $count) {
                $statusData[] = [ucfirst($status), $count];
            }
            $this->table(['Status', 'Count'], $statusData);
        }

        // Priority Distribution
        if (!empty($breakdown['breakdown']['byPriority'])) {
            $this->newLine();
            $this->line('<fg=cyan>Tasks by Priority:</>');
            $priorityData = [];
            foreach ($breakdown['breakdown']['byPriority'] as $priority => $count) {
                $priorityData[] = [ucfirst($priority), $count];
            }
            $this->table(['Priority', 'Count'], $priorityData);
        }

        // Milestones
        if ($breakdown['milestones']['total'] > 0) {
            $this->newLine();
            $this->line('<fg=cyan>Milestones:</>');
            $this->table(
                ['Metric', 'Count'],
                [
                    ['Total', $breakdown['milestones']['total']],
                    ['Completed', $breakdown['milestones']['completed']],
                    ['Remaining', $breakdown['milestones']['total'] - $breakdown['milestones']['completed']],
                ]
            );
        }

        // Estimated Completion
        try {
            $estimatedCompletion = $service->estimateCompletionDate($project);
            if ($estimatedCompletion) {
                $this->newLine();
                $this->line("<fg=cyan>Estimated Completion:</>  {$estimatedCompletion->format('M d, Y')} ({$estimatedCompletion->diffForHumans()})");
            }
        } catch (\Exception $e) {
            // Silently skip if estimation fails
        }
    }
}
