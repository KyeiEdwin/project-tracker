<?php

use App\Http\Controllers\AgileController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AgileDefinitionController;
use App\Http\Controllers\BacklogItemController;
use App\Http\Controllers\BudgetItemController;
use App\Http\Controllers\ChangeLogController;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\InitiationController;
use App\Http\Controllers\KickoffController;
use App\Http\Controllers\LessonLearnedController;
use App\Http\Controllers\MilestoneController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\QaTestController;
use App\Http\Controllers\QualityController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\RiskController;
use App\Http\Controllers\SprintController;
use App\Http\Controllers\StakeholderController;
use App\Http\Controllers\SubtaskController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TeamMemberController;
use App\Http\Controllers\TimeEntryController;
use App\Http\Controllers\WorkflowController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'create'])->name('login');
Route::post('/login', [AuthController::class, 'store'])->name('login.store');
Route::post('/logout', [AuthController::class, 'destroy'])->middleware('auth')->name('logout');

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::prefix('projects')->name('projects.')->group(function () {
    Route::get('/', [ProjectController::class, 'index'])->name('index');
    Route::get('/create', [ProjectController::class, 'create'])->name('create');
    Route::post('/', [ProjectController::class, 'store'])->name('store');
    Route::get('/{project}/edit', [ProjectController::class, 'edit'])->name('edit');
    Route::put('/{project}', [ProjectController::class, 'update'])->name('update');
    Route::patch('/{project}', [ProjectController::class, 'update']);
    Route::delete('/{project}', [ProjectController::class, 'destroy'])->name('destroy');
    Route::get('/{project}', [ProjectController::class, 'show'])->name('show');
});

Route::prefix('initiation')->name('initiation.')->group(function () {
    Route::get('/kickoff', [InitiationController::class, 'kickoff'])->name('kickoff');
    Route::get('/stakeholders', [InitiationController::class, 'stakeholders'])->name('stakeholders');
});

Route::prefix('agile')->name('agile.')->group(function () {
    Route::get('/sprints', [AgileController::class, 'sprints'])->name('sprints');
    Route::get('/backlog', [AgileController::class, 'backlog'])->name('backlog');
    Route::get('/definitions', [AgileController::class, 'definitions'])->name('definitions');
});

Route::prefix('tasks')->name('tasks.')->group(function () {
    Route::get('/', [TaskController::class, 'index'])->name('index');
    Route::get('/kanban', [TaskController::class, 'kanban'])->name('kanban');
    Route::get('/workflows', [WorkflowController::class, 'index'])->name('workflows');
    Route::get('/create', [TaskController::class, 'create'])->name('create');
    Route::post('/', [TaskController::class, 'store'])->name('store');
    Route::get('/{task}/edit', [TaskController::class, 'edit'])->name('edit');
    Route::put('/{task}', [TaskController::class, 'update'])->name('update');
    Route::delete('/{task}', [TaskController::class, 'destroy'])->name('destroy');
    Route::get('/{task}', [TaskController::class, 'show'])->name('show');
});

Route::prefix('resources')->name('resources.')->group(function () {
    Route::get('/team', [ResourceController::class, 'team'])->name('team');
    Route::get('/time-tracking', [ResourceController::class, 'timeTracking'])->name('time-tracking');
    Route::get('/budget', [ResourceController::class, 'budget'])->name('budget');
    Route::get('/milestones', [ResourceController::class, 'milestones'])->name('milestones');
    Route::get('/gantt', [ResourceController::class, 'gantt'])->name('gantt');
});

Route::prefix('quality')->name('quality.')->group(function () {
    Route::get('/qa-testing', [QualityController::class, 'qaTesting'])->name('qa-testing');
    Route::get('/risks', [QualityController::class, 'risks'])->name('risks');
    Route::get('/change-log', [QualityController::class, 'changeLog'])->name('change-log');
});

Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/analytics', [ReportController::class, 'analytics'])->name('analytics');
    Route::get('/documents', [DocumentController::class, 'index'])->name('documents');
    Route::get('/lessons-learned', [LessonLearnedController::class, 'index'])->name('lessons-learned');
});

Route::get('/chat', [ChatController::class, 'index'])->name('chat');

Route::resource('kickoffs', KickoffController::class);
Route::resource('stakeholders', StakeholderController::class);
Route::resource('team-members', TeamMemberController::class);
Route::resource('time-entries', TimeEntryController::class);
Route::resource('budget-items', BudgetItemController::class);
Route::resource('milestones', MilestoneController::class);
Route::resource('sprints', SprintController::class);
Route::resource('backlog-items', BacklogItemController::class);
Route::resource('agile-definitions', AgileDefinitionController::class);
Route::resource('subtasks', SubtaskController::class);
Route::resource('workflows', WorkflowController::class)->except(['index']);
Route::resource('qa-tests', QaTestController::class);
Route::resource('risks', RiskController::class);
Route::resource('change-logs', ChangeLogController::class);
Route::resource('reports', ReportController::class);
Route::resource('documents', DocumentController::class)->except(['index']);
Route::resource('lessons-learned', LessonLearnedController::class)->except(['index']);
Route::resource('charts', ChartController::class);
