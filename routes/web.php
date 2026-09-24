<?php

use App\Http\Controllers\AgileController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AgileDefinitionController;
use App\Http\Controllers\BacklogItemController;
use App\Http\Controllers\BudgetItemController;
use App\Http\Controllers\ChangeLogController;
use App\Http\Controllers\ChartController;
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
use App\Http\Controllers\TeamMemberAccountController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TeamMemberAuthController;
use App\Http\Controllers\TeamMemberChatController;
use App\Http\Controllers\TeamMemberDashboardController;
use App\Http\Controllers\TeamMemberTaskController;
use App\Http\Controllers\TimeEntryController;
use App\Http\Controllers\WorkflowController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'storeRegistration'])->name('register.store');
    Route::get('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'resetForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});
Route::post('/logout', [AuthController::class, 'destroy'])->middleware('auth')->name('logout');

Route::post('/team-member/logout', [TeamMemberAuthController::class, 'destroy'])
    ->middleware('auth:team_member')->name('team-member.logout');
Route::middleware('auth:team_member')->prefix('team-member')->name('team-member.')->group(function () {
    Route::get('/dashboard', TeamMemberDashboardController::class)
        ->middleware('can:dashboard.team_member.view')
        ->name('dashboard');
    Route::get('/chat', [TeamMemberChatController::class, 'index'])
        ->middleware('can:dashboard.team_member.view')
        ->name('chat');
    Route::post('/chat/messages', [TeamMemberChatController::class, 'store'])
        ->name('chat.messages.store');
    Route::patch('/tasks/{task}/status', [TeamMemberTaskController::class, 'updateStatus'])
        ->name('tasks.status');
    Route::get('/profile', [TeamMemberAccountController::class, 'profile'])
        ->middleware('can:profile.view')->name('profile');
    Route::put('/profile', [TeamMemberAccountController::class, 'updateProfile'])
        ->middleware('can:profile.edit')->name('profile.update');
    Route::get('/settings', [TeamMemberAccountController::class, 'settings'])
        ->middleware('can:setting.view')->name('settings');
    Route::patch('/settings/password', [TeamMemberAccountController::class, 'updatePassword'])
        ->middleware('can:setting.view')->name('settings.password');
});

Route::get('/email/verify', [AuthController::class, 'verificationNotice'])->middleware('auth')->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verify'])
    ->middleware(['auth', 'signed', 'throttle:6,1'])->name('verification.verify');
Route::post('/email/verification-notification', [AuthController::class, 'resendVerification'])
    ->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::middleware(['auth:web', 'verified-or-admin'])->group(function () {
Route::get('/', [DashboardController::class, 'index'])->middleware('can:dashboard.admin.view')->name('dashboard');
Route::post('/teams', [TeamController::class, 'store'])->middleware('can:user.manage')->name('teams.store');
Route::post('/teams/{team}/members', [TeamMemberController::class, 'addToTeam'])
    ->middleware('can:user.manage')->name('teams.members.store');

Route::prefix('projects')->name('projects.')->group(function () {
    Route::get('/', [ProjectController::class, 'index'])->middleware('can:project.view')->name('index');
    Route::get('/create', [ProjectController::class, 'create'])->middleware('can:project.create')->name('create');
    Route::post('/', [ProjectController::class, 'store'])->middleware('can:project.create')->name('store');
    Route::get('/{project}/dashboard', [ProjectController::class, 'dashboard'])->middleware('can:project.view')->name('dashboard');
    Route::get('/{project}/edit', [ProjectController::class, 'edit'])->middleware('can:project.update')->name('edit');
    Route::put('/{project}', [ProjectController::class, 'update'])->middleware('can:project.update')->name('update');
    Route::patch('/{project}', [ProjectController::class, 'update'])->middleware('can:project.update');
    Route::delete('/{project}', [ProjectController::class, 'destroy'])->middleware('can:project.delete')->name('destroy');
    Route::get('/{project}', [ProjectController::class, 'show'])->middleware('can:project.view')->name('show');
});

Route::prefix('initiation')->name('initiation.')->middleware('can:initiation.view')->group(function () {
    Route::get('/kickoff', [InitiationController::class, 'kickoff'])->name('kickoff');
    Route::get('/stakeholders', [InitiationController::class, 'stakeholders'])->name('stakeholders');
});

Route::prefix('agile')->name('agile.')->middleware('can:agile.view')->group(function () {
    Route::get('/sprints', [AgileController::class, 'sprints'])->name('sprints');
    Route::get('/backlog', [AgileController::class, 'backlog'])->name('backlog');
    Route::get('/definitions', [AgileController::class, 'definitions'])->name('definitions');
});

Route::prefix('tasks')->name('tasks.')->group(function () {
    Route::get('/', [TaskController::class, 'index'])->middleware('can:task.view')->name('index');
    Route::get('/kanban', [TaskController::class, 'kanban'])->middleware('can:task.view')->name('kanban');
    Route::get('/workflows', [WorkflowController::class, 'index'])->middleware('can:workflow.view')->name('workflows');
    Route::get('/create', [TaskController::class, 'create'])->middleware('can:task.create')->name('create');
    Route::post('/', [TaskController::class, 'store'])->middleware('can:task.create')->name('store');
    Route::get('/{task}/edit', [TaskController::class, 'edit'])->middleware('can:task.update')->name('edit');
    Route::put('/{task}', [TaskController::class, 'update'])->middleware('can:task.update')->name('update');
    Route::delete('/{task}', [TaskController::class, 'destroy'])->middleware('can:task.delete')->name('destroy');
    Route::get('/{task}', [TaskController::class, 'show'])->middleware('can:task.view')->name('show');
});

Route::prefix('resources')->name('resources.')->group(function () {
    Route::get('/team', [ResourceController::class, 'team'])->middleware('can:user.manage')->name('team');
    Route::get('/time-tracking', [ResourceController::class, 'timeTracking'])->middleware('can:time.view')->name('time-tracking');
    Route::get('/milestones', [ResourceController::class, 'milestones'])->middleware('can:milestone.view')->name('milestones');
    Route::get('/gantt', [ResourceController::class, 'gantt'])->middleware('can:milestone.view')->name('gantt');
});

Route::prefix('quality')->name('quality.')->middleware('can:quality.view')->group(function () {
    Route::get('/qa-testing', [QualityController::class, 'qaTesting'])->name('qa-testing');
    Route::get('/risks', [QualityController::class, 'risks'])->name('risks');
    Route::get('/change-log', [QualityController::class, 'changeLog'])->middleware('can:audit.view')->name('change-log');
});

Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/analytics', [ReportController::class, 'analytics'])->middleware('can:report.view')->name('analytics');
    Route::get('/documents', [DocumentController::class, 'index'])->middleware('can:report.view')->name('documents');
    Route::get('/lessons-learned', [LessonLearnedController::class, 'index'])->middleware('can:report.view')->name('lessons-learned');
});

Route::resource('kickoffs', KickoffController::class)->middleware('can:initiation.view');
Route::resource('stakeholders', StakeholderController::class)->middleware('can:stakeholder.view');
Route::resource('team-members', TeamMemberController::class)->middleware('can:user.manage');
Route::resource('time-entries', TimeEntryController::class)->middleware('can:time.view');
Route::resource('budget-items', BudgetItemController::class)->middleware('can:budget.manage');
Route::resource('milestones', MilestoneController::class)->middleware('can:milestone.view');
Route::resource('sprints', SprintController::class)->middleware('can:sprint.manage');
Route::resource('backlog-items', BacklogItemController::class)->middleware('can:agile.view');
Route::resource('agile-definitions', AgileDefinitionController::class)->middleware('can:agile.view');
Route::resource('subtasks', SubtaskController::class)->middleware('can:subtask.view');
Route::resource('workflows', WorkflowController::class)->except(['index'])->middleware('can:workflow.view');
Route::resource('qa-tests', QaTestController::class)->middleware('can:quality.view');
Route::resource('risks', RiskController::class)->middleware('can:quality.view');
Route::resource('change-logs', ChangeLogController::class)->middleware('can:audit.view');
Route::resource('reports', ReportController::class)->middleware('can:report.view');
Route::resource('documents', DocumentController::class)->except(['index'])->middleware('can:report.view');
Route::resource('lessons-learned', LessonLearnedController::class)->except(['index'])->middleware('can:report.view');
Route::resource('charts', ChartController::class)->middleware('can:chart.view');
Route::get('/profile', [AuthController::class, 'profile'])->middleware('can:profile.view')->name('profile');
Route::put('/profile', [AuthController::class, 'updateProfile'])->middleware('can:profile.update')->name('profile.update');
Route::get('/sessions', [AuthController::class, 'sessions'])->middleware('can:session.view')->name('sessions.index');
Route::delete('/sessions/{session}', [AuthController::class, 'revokeSession'])->middleware('can:session.revoke')->name('sessions.destroy');
Route::patch('/users/{user}/activate', fn (\App\Models\User $user, AuthController $controller) => $controller->setAccountStatus($user, true))
    ->middleware('can:user.manage')->name('users.activate');
Route::patch('/users/{user}/deactivate', fn (\App\Models\User $user, AuthController $controller) => $controller->setAccountStatus($user, false))
    ->middleware('can:user.manage')->name('users.deactivate');
});
