# Project Tracker Realtime Collaboration Rollout Plan

## Purpose

Introduce reliable project progress tracking and realtime collaboration while preserving the existing Vue 3 + Inertia frontend, Laravel CRUD workflows, filters, charts, navigation, and local development experience.

This plan is intentionally incremental. The application should remain usable when realtime infrastructure is disabled, unavailable, or still being developed.

## Current Baseline

The application currently provides:

- Laravel 12 backend with Eloquent models and resource controllers.
- Vue 3 pages delivered through Inertia.
- Project, task, subtask, milestone, sprint, backlog, budget, time tracking, risk, QA, reporting, and chat screens.
- Project and task progress fields in the database.
- Project detail statistics calculated by the controller.
- Laravel broadcasting configuration, but no broadcast events or Echo client yet.
- SQLite and synchronous/local development defaults.
- Chat UI with local mock messages rather than persisted messages.
- Some dashboard chart series still represented by demo values.

The first implementation target should be project detail and Kanban collaboration. Those screens already contain the core data and have a clear project context.

## Goals

1. Make project progress derive from persisted work instead of manual page values alone.
2. Update open project screens when another user changes a task or milestone.
3. Preserve normal Inertia form submissions and navigation.
4. Add realtime chat only after the event and channel foundation is proven.
5. Keep a polling or manual-refresh fallback for local development and degraded connections.
6. Keep existing pages working while realtime features are introduced incrementally.

## Non-Goals For The First Release

- Full offline editing or conflict-free replicated data.
- Replacing all Inertia navigation with a separate SPA API.
- Live cursor presence or document co-editing.
- Rebuilding every dashboard chart before the task workflow is reliable.
- Introducing microservices or a separate realtime backend.

## Recommended Architecture

```text
Vue/Inertia form
    -> Laravel controller and FormRequest
    -> Eloquent transaction
    -> progress recalculation
    -> domain event
    -> private project channel
    -> Echo listener in open Vue pages
```

Use Laravel Reverb as the first WebSocket implementation because it fits the existing Laravel application and avoids introducing a third-party hosted service during development. Keep the broadcast connection configurable so Pusher or Ably can be substituted later if deployment requirements change.

Use private channels named:

```text
project.{projectId}
```

Authorize subscription access in `routes/channels.php` once project membership/authentication is available. Until authentication is implemented, local development may use a temporary permissive policy only on a clearly isolated local environment; do not ship that policy to production.

## Progress Definition

Use one server-side progress rule. Do not let each Vue page calculate a different percentage.

Recommended first rule:

- If the project has estimated task hours, calculate a weighted average from task progress.
- If no estimates exist, calculate by completed task count.
- Exclude soft-deleted tasks.
- Return `0` when a project has no active tasks.
- Clamp the result to `0..100`.

Conceptually:

```text
weighted progress = sum(task.estimate_hours * task.progress) / sum(task.estimate_hours)
count progress    = completed tasks / active tasks * 100
```

The exact rule should be documented in code and covered by tests. Project `progress` remains a cached, query-friendly value; the progress service is the authority that refreshes it.

## Phase 1 - Stabilize The Data Contract

### Backend

1. Add a `ProjectProgressService` under `app/Services/`.
2. Add a small value/result shape if useful, containing:
   - project ID
   - progress percentage
   - active task count
   - completed task count
   - total estimated hours
   - timestamp
3. Add tests for:
   - no tasks
   - all tasks completed
   - partial task progress
   - weighted estimates
   - missing estimates fallback
   - soft-deleted tasks
4. Add a single method or domain action that recalculates progress after a task, subtask, milestone, or relevant time entry change.
5. Use a database transaction for the write and progress update where the operation changes related records.

### API/Inertia contract

Expose a consistent project progress payload from:

- Project detail
- Dashboard project metrics
- Project list rows where progress is shown
- Kanban responses when a project filter is active

Keep the existing camelCase `toInertia()` format. Do not force a frontend rewrite to adopt a new API layer.

### Frontend

1. Replace hard-coded project progress displays with the server-provided value.
2. Keep existing page layout, tabs, forms, charts, and navigation.
3. Add a small `last updated` indicator only where it improves trust; it should not dominate the UI.

### Acceptance criteria

- A task status/progress update changes the project progress in the database.
- Project detail and dashboard display the same value after a normal Inertia reload.
- Existing project and task feature tests continue to pass.

## Phase 2 - Add Domain Events Without WebSockets Yet

Create events that describe committed changes:

- `TaskCreated`
- `TaskUpdated`
- `TaskDeleted`
- `MilestoneUpdated`
- `ProjectProgressUpdated`

Each project-scoped event should include:

- project ID
- actor/user ID when authentication exists
- changed record or a serialized minimal payload
- new progress summary when relevant
- event version or timestamp if ordering matters

Use `ShouldBroadcast` only after the event payload and authorization are tested. During the first phase, listeners can be used internally or events can broadcast to the configured log driver for inspection.

Avoid broadcasting entire database collections. Send a changed task/milestone plus the progress summary, then let the receiving page update its local state or request an Inertia partial reload.

## Phase 3 - Realtime Task And Project Progress

### Dependencies

Add the Laravel Reverb server package and Laravel Echo plus the WebSocket client used by the selected Echo configuration. Keep versions compatible with Laravel 12 and the existing Vite build.

Do not hard-code credentials. Add Reverb settings to `.env.example`, with local-safe values and comments explaining which process starts the server.

### Development processes

The local development workflow should be:

```text
php artisan serve       Laravel HTTP server
npm run dev             Vite development server
php artisan reverb:start WebSocket server
```

A queue worker may be added when broadcasts are queued:

```text
php artisan queue:work
```

The application must still function when the Reverb process is stopped. The UI should show a disconnected state and offer polling/manual refresh rather than failing page loads.

### Frontend integration

Create one small Echo bootstrap module, for example:

```text
resources/js/realtime/echo.js
```

Create a composable for project subscriptions, for example:

```text
resources/js/composables/useProjectRealtime.js
```

The composable should:

- Subscribe only when a valid project ID exists.
- Register and remove listeners during component mount/unmount.
- Avoid duplicate subscriptions when Inertia navigates between pages.
- Expose connection status.
- Provide a fallback refresh function.
- Clean up channels on unmount.

Subscribe project detail, Kanban, Gantt, and project-filtered resource pages first.

On `TaskUpdated`:

- Replace the matching task in the local list, or move it between Kanban columns.
- Update the project progress summary.
- Show a subtle "updated by another user" indicator when appropriate.

On `ProjectProgressUpdated`:

- Update the progress bar and related KPI values.
- Update charts only when their data is actually derived from the event.
- Avoid a full page refresh for a simple progress change.

For complex pages where several server-side totals change, perform an Inertia partial reload:

```js
router.reload({
  only: ['project', 'tasks', 'stats'],
  preserveScroll: true,
})
```

### Fallback behavior

Use this priority order:

1. WebSocket event when connected.
2. Inertia partial reload after the current user submits a change.
3. Polling every 15 to 30 seconds while the page is visible if WebSockets are unavailable.
4. Manual refresh action as the final fallback.

Polling must pause when the browser tab is hidden and resume when it becomes visible. Prevent overlapping requests.

## Phase 4 - Make Kanban Collaboration Real

The current Kanban presentation should be extended carefully:

1. Persist drag-and-drop status changes through a dedicated task update request.
2. Persist `kanban_order` for ordering.
3. Broadcast the resulting task update after the transaction commits.
4. Apply remote changes to the current board without losing an in-progress local drag.
5. Refresh the board if a conflict is detected.
6. Add feature tests for status, order, project ownership, and authorization.

Use optimistic UI only for the local drag operation. If the server rejects the update, restore the previous position and show the validation error.

## Phase 5 - Replace Mock Chat With Realtime Chat

Chat should be treated as a separate collaboration slice after task events work.

### Backend

Add:

- `messages` table
- `Message` model
- project/channel relationship
- request validation
- message create endpoint
- pagination ordered by creation time
- `ChatMessageCreated` broadcast event
- authorization for project/channel membership

Messages should be persisted before they are broadcast. The sender should receive the server-created message ID and timestamp, not invent either value in Vue.

### Frontend

Replace the local hard-coded message array with:

- Initial messages from the controller.
- Form submission through Inertia or a JSON endpoint.
- Echo listener for new messages.
- De-duplication by message ID.
- Loading, failed-send, empty, and disconnected states.
- Incremental loading for older messages.

Presence indicators can be added later. They are not required for the first chat release.

## Phase 6 - Live Dashboard And Reporting

After event delivery is stable:

1. Replace hard-coded dashboard KPI values with database queries.
2. Add a project progress history table or daily snapshot job if trend charts are required.
3. Broadcast only summary changes needed by open dashboard clients.
4. Use periodic refresh for historical analytics rather than broadcasting every row-level event.
5. Cache expensive report queries and invalidate them when relevant domain events occur.

A dashboard does not need to receive every task payload. It can receive a `DashboardMetricsUpdated` event or perform a partial reload of metrics.

## Authentication And Authorization Prerequisites

Realtime project channels require a trustworthy identity and project membership rule.

Before production WebSockets:

- Add or confirm Laravel authentication.
- Define project membership/ownership authorization.
- Protect write routes with authorization policies.
- Authorize private channel subscriptions.
- Ensure event payloads do not expose private project data.
- Add tests for allowed and denied subscriptions.

## Testing Strategy

### Backend tests

- Progress service unit tests.
- Controller tests for recalculation after task and milestone changes.
- Event dispatch tests.
- Broadcast channel authorization tests.
- Message persistence and authorization tests.
- Queue/broadcast failure behavior where applicable.

### Frontend checks

- Vue build with realtime disabled.
- Vue build with realtime enabled.
- Subscription cleanup on navigation.
- Duplicate event de-duplication.
- Polling fallback and tab visibility behavior.
- Kanban remote update behavior.

### Manual two-browser test

1. Open the same project in two browser windows.
2. Move or update a task in window A.
3. Confirm window B receives the task and progress update.
4. Stop Reverb and confirm both pages remain usable.
5. Confirm polling or manual refresh restores current data.
6. Send a chat message from window A after chat is implemented.

## Deployment Shape

For a first production deployment, use separate long-running processes for:

- PHP application server/FPM
- queue worker, if queues are enabled
- Reverb WebSocket server
- Vite-built static assets served by the web server

Use Redis for queues/cache when the application grows beyond a single local instance. SQLite is appropriate for local development and small demos, but use MySQL or PostgreSQL for concurrent production writes and reliable scaling.

## Suggested Delivery Milestones

### Milestone A: Reliable progress

- Progress service
- Recalculation hooks
- Server-derived project metrics
- Tests

### Milestone B: Realtime task updates

- Events
- Reverb/Echo setup
- Private project channel
- Project detail and Kanban listeners
- Polling fallback

### Milestone C: Collaborative Kanban

- Persisted drag-and-drop
- Ordering
- Conflict and rollback behavior

### Milestone D: Realtime chat

- Persisted messages
- Message event
- Chat subscriptions
- Pagination and failure states

### Milestone E: Live analytics

- Database-derived dashboard metrics
- Progress history
- Summary events and caching

## Review Decisions Needed Before Implementation

1. Should progress be weighted by estimated hours, task count, or a project-selectable rule?
2. Is Laravel authentication already planned, or should the first realtime prototype run only in a local trusted environment?
3. Should Reverb run locally as the default developer experience, or should polling remain the default until deployment?
4. Should chat be scoped to projects only, or should global channels remain?
5. Which screens are required to update live in the first release: project detail, Kanban, dashboard, Gantt, chat, or all of them?
6. Should task updates be optimistic in the UI, or should the UI wait for the server response for the first release?

## Recommended First Implementation Slice

Start with Milestone A and the first half of Milestone B:

- Add `ProjectProgressService`.
- Recalculate progress from task changes.
- Add `ProjectProgressUpdated` and `TaskUpdated`.
- Configure Reverb/Echo behind environment flags.
- Subscribe only `Projects/Show` and `Tasks/Kanban`.
- Add polling fallback.
- Leave chat and dashboard chart replacement for later milestones.

This gives the project a useful realtime capability while keeping the existing Vue frontend and the current Laravel/Inertia development workflow intact.
