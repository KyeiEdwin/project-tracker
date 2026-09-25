<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use App\Models\BacklogItem;
use App\Models\Sprint;
use App\Models\Task;
use App\Models\TeamMember;
use App\Models\Project;
use App\Models\Milestone;
use App\Policies\ProjectPolicy;
use App\Policies\TaskPolicy;
use App\Observers\BacklogItemObserver;
use App\Observers\SprintObserver;
use App\Observers\ProjectObserver;
use App\Observers\TaskObserver;
use App\Observers\MilestoneObserver;
use App\Observers\TeamMemberObserver;
use App\Events\TaskUpdated;
use App\Events\ProjectProgressUpdated;
use App\Listeners\InvalidateTaskMetricsCache;
use App\Listeners\InvalidateProjectMetricsCache;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register policies
        Gate::policy(Task::class, TaskPolicy::class);
        Gate::policy(Project::class, ProjectPolicy::class);

        // Register permissions
        foreach (config('authorization.permissions', []) as $permission) {
            Gate::define($permission, fn ($user): bool => $user->hasPermission($permission));
        }

        Gate::define('team.manage', fn (TeamMember $member): bool => $member->hasPermission('team.manage'));

        // Register model observers for dashboard metrics cache invalidation
        Project::observe(ProjectObserver::class);
        Task::observe(TaskObserver::class);
        Milestone::observe(MilestoneObserver::class);
        TeamMember::observe(TeamMemberObserver::class);

        // Register Agile Module observers
        BacklogItem::observe(BacklogItemObserver::class);
        Sprint::observe(SprintObserver::class);

        // Register event listeners
        Event::listen(TaskUpdated::class, InvalidateTaskMetricsCache::class);
        Event::listen(ProjectProgressUpdated::class, InvalidateProjectMetricsCache::class);
    }
}
