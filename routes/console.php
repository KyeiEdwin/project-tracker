<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule daily sprint snapshots at midnight
Schedule::command('sprints:create-snapshots')
    ->daily()
    ->at('00:00')
    ->withoutOverlapping()
    ->onSuccess(function () {
        \Log::info('Daily sprint snapshots created successfully');
    })
    ->onFailure(function () {
        \Log::error('Failed to create daily sprint snapshots');
    });
