<?php

namespace App\Observers;

use App\Models\TeamMember;
use App\Services\DashboardMetricsService;

class TeamMemberObserver
{
    /**
     * Create the observer.
     */
    public function __construct(
        protected DashboardMetricsService $metricsService
    ) {}

    /**
     * Handle the TeamMember "created" event.
     */
    public function created(TeamMember $teamMember): void
    {
        $this->invalidateTeamMetrics();
    }

    /**
     * Handle the TeamMember "deleted" event.
     */
    public function deleted(TeamMember $teamMember): void
    {
        $this->invalidateTeamMetrics();
    }

    /**
     * Handle the TeamMember "restored" event.
     */
    public function restored(TeamMember $teamMember): void
    {
        $this->invalidateTeamMetrics();
    }

    /**
     * Invalidate team member metrics
     */
    protected function invalidateTeamMetrics(): void
    {
        $this->metricsService->invalidateMetric('team_members');
    }
}
