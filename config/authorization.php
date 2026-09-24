<?php

return [
    'permissions' => [
        'project.create', 'project.view', 'project.update', 'project.delete',
        'task.create', 'task.view', 'task.assign', 'task.update', 'task.delete',
        'sprint.create', 'sprint.manage', 'budget.view', 'budget.manage',
        'report.view', 'user.manage', 'audit.view',
        'dashboard.admin.view', 'dashboard.team_member.view',
        'member.dashboard.view', 'member.project.view', 'member.task.view',
        'member.task.status.update', 'member.task.complete',
        'initiation.view', 'agile.view', 'quality.view',
        'time.view', 'milestone.view', 'stakeholder.view',
        'chart.view', 'workflow.view', 'subtask.view',
        'profile.view', 'profile.edit', 'profile.update', 'setting.view',
        'session.view', 'session.revoke', 'team.manage',
    ],
];