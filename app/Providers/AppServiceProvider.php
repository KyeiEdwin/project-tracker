<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use App\Models\Task;
use App\Models\TeamMember;
use App\Models\Project;
use App\Policies\ProjectPolicy;
use App\Policies\TaskPolicy;

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
        Gate::policy(Task::class, TaskPolicy::class);
        Gate::policy(Project::class, ProjectPolicy::class);

        foreach (config('authorization.permissions', []) as $permission) {
            Gate::define($permission, fn ($user): bool => $user->hasPermission($permission));
        }

        Gate::define('team.manage', fn (TeamMember $member): bool => $member->hasPermission('team.manage'));
    }
}
