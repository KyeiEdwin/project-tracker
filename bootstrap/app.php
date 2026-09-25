<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;
use App\Http\Middleware\EnsureEmailVerifiedOrAdmin;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        channels: __DIR__.'/../routes/channels.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
        ]);
        $middleware->alias([
            'verified-or-admin' => EnsureEmailVerifiedOrAdmin::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
        // Refresh dashboard metrics cache every 15 minutes
        $schedule->command('dashboard:refresh-metrics')
            ->everyFifteenMinutes()
            ->withoutOverlapping()
            ->runInBackground();

        // Clean up expired cache entries daily at 2 AM
        $schedule->command('dashboard:cleanup-cache')
            ->dailyAt('02:00')
            ->withoutOverlapping();

        // Warm up cache every hour to ensure it's always fresh
        $schedule->command('dashboard:refresh-metrics --force')
            ->hourly()
            ->withoutOverlapping()
            ->runInBackground();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
