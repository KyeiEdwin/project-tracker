<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\InitiationController;
use App\Http\Controllers\AgileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\QualityController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

// Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Projects
Route::prefix('projects')->name('projects.')->group(function () {
    Route::get('/', [ProjectController::class, 'index'])->name('index');
    Route::get('/create', [ProjectController::class, 'create'])->name('create');
    Route::post('/', [ProjectController::class, 'store'])->name('store');
    Route::get('/{project}', [ProjectController::class, 'show'])->name('show');
});

// Initiation
Route::prefix('initiation')->name('initiation.')->group(function () {
    Route::get('/kickoff', [InitiationController::class, 'kickoff'])->name('kickoff');
    Route::get('/stakeholders', [InitiationController::class, 'stakeholders'])->name('stakeholders');
});

// Agile
Route::prefix('agile')->name('agile.')->group(function () {
    Route::get('/sprints', [AgileController::class, 'sprints'])->name('sprints');
    Route::get('/backlog', [AgileController::class, 'backlog'])->name('backlog');
    Route::get('/definitions', [AgileController::class, 'definitions'])->name('definitions');
});

// Tasks
Route::prefix('tasks')->name('tasks.')->group(function () {
    Route::get('/', [TaskController::class, 'index'])->name('index');
    Route::get('/kanban', [TaskController::class, 'kanban'])->name('kanban');
    Route::get('/workflows', [TaskController::class, 'workflows'])->name('workflows');
});

// Resources
Route::prefix('resources')->name('resources.')->group(function () {
    Route::get('/team', [ResourceController::class, 'team'])->name('team');
    Route::get('/time-tracking', [ResourceController::class, 'timeTracking'])->name('time-tracking');
    Route::get('/budget', [ResourceController::class, 'budget'])->name('budget');
    Route::get('/milestones', [ResourceController::class, 'milestones'])->name('milestones');
    Route::get('/gantt', [ResourceController::class, 'gantt'])->name('gantt');
});

// Quality
Route::prefix('quality')->name('quality.')->group(function () {
    Route::get('/qa-testing', [QualityController::class, 'qaTesting'])->name('qa-testing');
    Route::get('/risks', [QualityController::class, 'risks'])->name('risks');
    Route::get('/change-log', [QualityController::class, 'changeLog'])->name('change-log');
});

// Reports
Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/analytics', [ReportController::class, 'analytics'])->name('analytics');
    Route::get('/documents', [ReportController::class, 'documents'])->name('documents');
    Route::get('/lessons-learned', [ReportController::class, 'lessonsLearned'])->name('lessons-learned');
});

// Communication / Chat
Route::get('/chat', [ChatController::class, 'index'])->name('chat');
