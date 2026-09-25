<?php

namespace App\Http\Controllers;

use App\Events\ProjectProgressUpdated;
use App\Events\TaskUpdated;
use App\Http\Requests\UpdateTeamMemberTaskStatusRequest;
use App\Models\AuditLog;
use App\Models\Task;
use App\Models\TeamMember;
use App\Services\ProjectProgressService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Broadcasting\BroadcastException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class TeamMemberTaskController extends Controller
{
    public function updateStatus(
        UpdateTeamMemberTaskStatusRequest $request,
        Task $task,
        ProjectProgressService $progressService,
    ): RedirectResponse {
        /** @var TeamMember $member */
        $member = $request->user('team_member');
        $task = $member->assignedTasks()->whereKey($task->id)->firstOrFail();
        $status = $request->validated('status');

        abort_unless(
            $member->hasPermission('member.task.view')
                && $member->hasPermission('member.task.status.update')
                && (! in_array($status, ['completed', 'done'], true)
                    || $member->hasPermission('member.task.complete'))
                && Gate::forUser($member)->allows('updateStatusForTeamMember', $task),
            404,
        );

        $oldStatus = $task->status;

        DB::transaction(function () use ($member, $task, $status, $oldStatus, $request): void {
            $task->update([
                'status' => $status,
                'progress' => in_array($status, ['completed', 'done'], true)
                    ? 100
                    : $task->progress,
            ]);

            AuditLog::query()->create([
                'actor_type' => $member::class,
                'actor_id' => $member->id,
                'action' => 'task.status_changed',
                'auditable_type' => $task::class,
                'auditable_id' => $task->id,
                'project_id' => $task->project_id,
                'task_id' => $task->id,
                'old_values' => ['status' => $oldStatus],
                'new_values' => ['status' => $status],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        });

        // Recalculate project progress
        $progress = $progressService->calculateProgress($task->project, update: true);
        
        try {
            event(new TaskUpdated($task->project_id, $task->fresh()->toInertia(), $progress));
            event(new ProjectProgressUpdated($task->project_id, $progress));
        } catch (BroadcastException $exception) {
            Log::warning('Realtime task broadcast skipped.', [
                'project_id' => $task->project_id,
                'task_id' => $task->id,
                'error' => $exception->getMessage(),
            ]);
        }

        return back()->with('success', 'Task status updated.');
    }
}