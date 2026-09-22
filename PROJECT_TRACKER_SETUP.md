# Project Tracker database and API setup

## Overview

Project Tracker is a Laravel 12, Inertia.js, Vue 3 application backed by PostgreSQL. The backend owns the application data: controllers query Eloquent models, serialize them into Inertia-safe arrays, and return the page props consumed by `resources/js/Pages`.

The schema is organized around a project and its planning, agile delivery, resource, quality, reporting, and charting records. Destructive user actions use soft deletes for primary business records where recovery is useful; dependent records use database-level cascading or nulling foreign keys.

## Relationship map

```text
Project
├── Kickoff ──< KickoffObjective
├── Stakeholder
├── BudgetItem
├── Milestone ──< Task
├── Sprint ──< Task
│           └── BacklogItem
├── BacklogItem ──> Task (optional)
├── AgileDefinition
├── Task ──< Subtask
│         ├── TaskDependency >── Task
│         ├── TimeEntry
│         └── BacklogItem
├── Workflow ──< WorkflowState
│             └── Task
├── QaTest ──< QaTestStep
├── Risk
├── ChangeLog
├── Report
├── Document
├── LessonLearned
└── Chart ──< ChartSeries

Project >──< TeamMember (project_team_member)
TeamMember ──< TimeEntry, Task, Subtask
```

## Setup

### Prerequisites

- PHP 8.2 or later (the project has been verified with Herd PHP 8.4)
- Composer
- Node.js and npm
- PostgreSQL, with the credentials in `.env`

### Install and configure

```powershell
composer install
npm install
Copy-Item .env.example .env -ErrorAction SilentlyContinue
php artisan key:generate
```

Configure PostgreSQL in `.env`:

```dotenv
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=project_tracker
DB_USERNAME=postgres
DB_PASSWORD=
```

Then build the schema and frontend:

```powershell
php artisan migrate --no-interaction
npm run build
composer test
```

`composer test` runs PHPUnit using an in-memory SQLite database specified by `phpunit.xml`; it never changes the configured PostgreSQL database.

### Artisan generation commands

The existing files are already generated and migrated. For a clean recreation, generate parent models before their children:

```powershell
php artisan make:model Project -m
php artisan make:model Kickoff -m
php artisan make:model KickoffObjective -m
php artisan make:model Stakeholder -m
php artisan make:model TeamMember -m
php artisan make:model TimeEntry -m
php artisan make:model BudgetItem -m
php artisan make:model Milestone -m
php artisan make:model Sprint -m
php artisan make:model BacklogItem -m
php artisan make:model AgileDefinition -m
php artisan make:model Task -m
php artisan make:model Subtask -m
php artisan make:model TaskDependency -m
php artisan make:model Workflow -m
php artisan make:model WorkflowState -m
php artisan make:model QaTest -m
php artisan make:model QaTestStep -m
php artisan make:model Risk -m
php artisan make:model ChangeLog -m
php artisan make:model Report -m
php artisan make:model Document -m
php artisan make:model LessonLearned -m
php artisan make:model Chart -m
php artisan make:model ChartSeries -m
```

After generation, apply the project migration definitions and run `php artisan migrate --no-interaction`. The checked-in migrations are the source of truth for columns, indexes, foreign keys, timestamps, and soft-delete behavior.

## Tables and models

| Section | Table / model | Purpose and key columns |
| --- | --- | --- |
| Projects | `projects` / `Project` | Root entity: name, slug, type, priority, status, dates, budget, progress, client, settings. |
| Initiation | `kickoffs` / `Kickoff` | Project kickoff schedule, agenda, notes, status. |
| Initiation | `kickoff_objectives` / `KickoffObjective` | Ordered, completion-tracked goals for a kickoff. |
| Initiation | `stakeholders` / `Stakeholder` | Project contacts with role, influence, interest, and notes. |
| Resources | `team_members` / `TeamMember` | Reusable team directory with unique email, availability, rate, and status. |
| Resources | `project_team_member` | Project/team-member pivot with allocation percentage. |
| Resources | `time_entries` / `TimeEntry` | Project/team-member work date, hours, billability, optional task. |
| Resources | `budget_items` / `BudgetItem` | Project allocation and spending by category. |
| Resources | `milestones` / `Milestone` | Project due dates, status, completion timestamp, and order. |
| Agile | `sprints` / `Sprint` | Project iterations with dates, points, goal, and status. |
| Agile | `backlog_items` / `BacklogItem` | Ranked stories, bugs, or tasks; optionally assigned to a sprint/task. |
| Agile | `agile_definitions` / `AgileDefinition` | Project/global definition-of-ready or definition-of-done checklist. |
| Tasks | `tasks` / `Task` | Core work item with project, sprint, milestone, workflow, assignee, schedule, priority, and progress. |
| Tasks | `subtasks` / `Subtask` | Ordered task children with optional assignee and due date. |
| Tasks | `task_dependencies` / `TaskDependency` | Directed task-to-task dependency with a unique pair constraint. |
| Tasks | `workflows` / `Workflow` | Project/global workflow, description, and default flag. |
| Tasks | `workflow_states` / `WorkflowState` | Ordered workflow states with slug, color, initial/final flags. |
| Quality | `qa_tests` / `QaTest` | Test cases by project, type, owner, priority, and execution status. |
| Quality | `qa_test_steps` / `QaTestStep` | Ordered test instructions, expected/actual results, and status. |
| Quality | `risks` / `Risk` | Project risk category, probability, impact, owner, mitigation, and state. |
| Quality | `change_logs` / `ChangeLog` | Change request metadata, impact, requestor, date, and status. |
| Reports | `reports` / `Report` | Report type, period, metrics JSON, optional project, and generated time. |
| Reports | `documents` / `Document` | Project document metadata: disk, path, size, MIME type, and uploader. |
| Reports | `lessons_learned` / `LessonLearned` | Project observations categorized by impact and recorded date. |
| Charts | `charts` / `Chart` | Project/global chart type, datasource, configuration JSON, and visibility. |
| Charts | `chart_series` / `ChartSeries` | Ordered named data series with color and JSON data. |

All models declare `$fillable` fields and casts for dates, numbers, booleans, and JSON where applicable. Relationship methods are in `app/Models`; `Project` exposes the major project-scoped one-to-many relationships and the `teamMembers()` many-to-many relationship.

## Validation

Store and update requests live in `app/Http/Requests`. Update requests reuse the store rules, so validation is applied before every persistence operation.

| Area | Key validation |
| --- | --- |
| Projects | Required name/priority/status; valid dates; end date on/after start; non-negative budget/spend; progress 0–100. |
| Initiation | Existing project references; valid kickoff date/status; stakeholder email/role/influence/interest; nested objectives validated. |
| Resources | Unique team-member email; 0–100 availability/allocation; existing project/member/task references; non-negative hours and money. |
| Agile | Existing project/sprint/task references; sprint end date on/after start; allowed backlog and definition types/statuses. |
| Tasks | Required project/title/status/priority; valid optional relation IDs; due date on/after start; progress 0–100; dependencies must reference existing tasks. |
| Quality | Existing project references; QA type/status/priority; risk probability/impact; change request dates and allowed status values. |
| Reports | Existing optional project references; report/chart types; JSON-array configuration/series payloads; document metadata limits. |
| Nested records | Kickoff objectives, QA steps, workflow states, chart series, and task dependencies are validated as nested arrays by their parent form requests. |

The `TrackerFormRequest` base class also normalizes camelCase request keys to snake_case so Vue forms can send either convention.

## CRUD and Inertia endpoints

The primary CRUD endpoints are registered in `routes/web.php`. Each resource supports `index`, `create`, `store`, `show`, `edit`, `update`, and `destroy`, unless noted below.

| Resource route | Controller | Inertia page |
| --- | --- | --- |
| `/projects` | `ProjectController` | `Projects/Index`, `Projects/Create`, `Projects/Show` |
| `/kickoffs`, `/stakeholders` | `KickoffController`, `StakeholderController` | `Initiation/Kickoff`, `Initiation/Stakeholders` |
| `/team-members`, `/time-entries`, `/budget-items`, `/milestones` | Resource controllers | Resource pages |
| `/sprints`, `/backlog-items`, `/agile-definitions` | Resource controllers | Agile pages |
| `/tasks`, `/subtasks`, `/workflows` | Resource controllers | Task pages |
| `/qa-tests`, `/risks`, `/change-logs` | Resource controllers | Quality pages |
| `/reports`, `/documents`, `/lessons-learned` | Resource controllers | Report pages |
| `/charts` | `ChartController` | `Charts/Index` |

Section navigation routes such as `/agile/sprints`, `/resources/team`, `/quality/risks`, and `/reports/analytics` return the corresponding Inertia pages with backend-fetched props.

Nested child records are managed atomically through their parent CRUD controllers:

- Kickoff create/update synchronizes kickoff objectives.
- QA test create/update synchronizes QA test steps.
- Task create/update synchronizes task dependencies; task deletion removes its nested records.
- Workflow create/update synchronizes workflow states.
- Chart create/update synchronizes chart series.

## Integrity and lifecycle rules

- Project-owned records use foreign keys with `cascadeOnDelete` where the child has no meaning without its project.
- Optional links such as task-to-sprint, task-to-milestone, task-to-workflow, and backlog/time-entry-to-task use `nullOnDelete` where retaining the child is useful.
- `task_dependencies` prevents duplicate dependency pairs with a composite unique index.
- Soft deletes are enabled for projects and primary user-managed records such as tasks, milestones, sprints, risks, reports, and charts. Parent controllers explicitly remove nested records when updating or deleting their owning record.
- Frequently queried columns are indexed, including project/status pairs, dates, priorities, sort orders, and foreign keys.

## Verification status

The local PostgreSQL database has all migrations applied. Route registration reports 158 routes. The frontend production build completes successfully. The automated feature test in `tests/Feature/TaskCreationTest.php` verifies task creation and dependency persistence using an isolated SQLite database:

```powershell
composer test
```

Expected result:

```text
OK (1 test, 4 assertions)
```

## Troubleshooting

| Symptom | Resolution |
| --- | --- |
| `php` is not recognized | Use Herd's PHP executable or add Herd's selected PHP version to `PATH`. |
| PostgreSQL connection error | Confirm the PostgreSQL service is running and the `.env` database, user, password, and port match the local server. Then run `php artisan config:clear`. |
| Migration is pending | Run `php artisan migrate:status`, then `php artisan migrate --no-interaction`. |
| Foreign-key migration failure | Do not change migration order; parent tables must precede child tables. Existing migrations already sequence deferred task-related foreign keys last. |
| Test database error | Keep `phpunit.xml` at the repository root. It configures SQLite `:memory:` and a valid test-only app key. |
| Vue page has missing props | Verify the matching controller renders the documented Inertia page and run `npm run build` after frontend changes. |
| Generated asset/font warnings | Vite can leave legacy static asset URLs for runtime resolution; confirm the referenced files remain under `public/`. |
