# Agile Module Enhancement - Implementation Plan

## Executive Summary

This document outlines the comprehensive plan to enhance the Agile Module with hierarchical backlog management, automated sprint calculations, proper lifecycle management, and advanced metrics tracking.

## Problem Statement

The current Agile Module lacks:
- Hierarchical backlog organization (Epic → Feature → Story → Task relationships)
- Automated sprint point calculations
- Proper sprint lifecycle management with validation
- Comprehensive metrics tracking (velocity, burndown charts)
- Historical data preservation and audit trails
- Cross-project validation and data integrity checks

These limitations prevent effective agile/scrum methodology adoption and accurate historical reporting.

## Requirements Analysis

### 1. Backlog Hierarchy
**Goal:** Support Epic → Feature → Story → Task relationships

**Requirements:**
- Parent-child relationships via `parent_id` field
- Scoped ranking (items ranked within their parent context)
- Hybrid UI: Both dedicated hierarchy view AND inline quick-add
- Validation to ensure parent/project consistency
- Prevent circular references
- Support drag-and-drop hierarchy reorganization

**Success Criteria:**
- Can create multi-level backlog hierarchies
- Items can be reordered within their parent scope
- Cross-project parent assignments are prevented
- UI clearly shows hierarchical relationships

### 2. Automatic Sprint Calculations
**Goal:** Eliminate manual point entry, automate all calculations

**Requirements:**
- Centralized `SprintService` for all sprint business logic
- Immediate recalculation via observers when backlog items change
- Remove manual editing of `planned_points` and `completed_points`
- Automatic point aggregation from assigned backlog items
- Calculate completed points from items marked as 'done'

**Success Criteria:**
- Adding item to sprint immediately updates planned_points
- Marking item done immediately updates completed_points
- Moving items between sprints updates both sprints
- Manual point editing is disabled

### 3. Sprint Lifecycle Management
**Goal:** Enforce proper sprint state transitions

**Requirements:**
- Explicit API endpoints for start/close operations
- Status flow: `planned` → `active` → `completed`
- One active sprint per project at a time
- Auto-close expired sprints when starting new sprint
- Hard lock on completed sprints (immutable)
- Validate sprint can be started (has items, valid dates)

**Success Criteria:**
- Cannot start second active sprint in same project
- Starting sprint auto-closes expired active sprints
- Cannot modify completed sprints
- Clear error messages for invalid transitions

### 4. Metrics & Analytics
**Goal:** Provide comprehensive sprint and velocity analytics

**Requirements:**
- Progress calculation: `completed_points / planned_points`
- Velocity tracking: average completed points over last N sprints
- Burndown chart data generation with ideal vs actual lines
- Daily snapshot storage for historical accuracy
- Cache metrics appropriately (active vs completed sprints)

**Success Criteria:**
- Burndown chart shows accurate historical progression
- Velocity correctly averages across completed sprints
- Metrics update in real-time as work progresses
- Historical data remains accurate after sprint completion

### 5. Sprint History & Audit
**Goal:** Maintain comprehensive audit trail

**Requirements:**
- Detailed event tracking (items added/removed, estimates changed, status transitions)
- Immutable historical records for completed sprints
- Sprint event log table with user attribution
- Events: sprint_started, sprint_closed, item_added, item_removed, estimate_changed, scope_changed
- Integration with existing change log infrastructure

**Success Criteria:**
- All sprint actions are logged with timestamps and users
- Event log is queryable via API
- Audit trail supports compliance and retrospectives
- Events include relevant metadata (what changed, old/new values)

### 6. Cross-Project Validation
**Goal:** Ensure data integrity across projects

**Requirements:**
- Database constraints + application-layer validation (defense in depth)
- Prevent assigning backlog items with parents from different projects
- Prevent assigning items from different projects to same sprint
- Prevent sprints from containing items from different projects
- Clear validation error messages

**Success Criteria:**
- Cannot create invalid cross-project relationships
- Database constraints prevent direct SQL manipulation
- Application layer provides user-friendly error messages
- Existing valid data is not affected

## Current Architecture Analysis

### Existing Infrastructure
- **Framework:** Laravel 11 with Inertia.js (Vue 3) frontend
- **Design Patterns:**
  - Service layer pattern (ProjectProgressService, DashboardMetricsService)
  - Observer pattern (TaskObserver, ProjectObserver, MilestoneObserver)
  - Form request validation with TrackerFormRequest base class
- **Data Features:**
  - Soft deletes enabled on all models
  - Metrics caching infrastructure in place
  - Event broadcasting for real-time updates

### Current Models

**Sprint Model:**
```php
- id
- project_id
- name
- goal
- start_date
- end_date
- status ('planned', 'active', 'completed')
- planned_points (currently manually editable - WILL BE REMOVED)
- completed_points (currently manually editable - WILL BE REMOVED)
- timestamps
- deleted_at
```

**BacklogItem Model:**
```php
- id
- project_id
- sprint_id (nullable)
- task_id (nullable)
- title
- description
- type ('epic', 'feature', 'story', 'bug', 'spike')
- points (story points estimate)
- priority ('low', 'medium', 'high', 'critical')
- status ('backlog', 'ready', 'in-progress', 'done')
- rank (ordering within project)
- timestamps
- deleted_at
```

### Existing Validation Patterns
- Custom form requests extending `TrackerFormRequest`
- Rule-based validation with Laravel's validation rules
- Custom validation messages
- Authorization checks via policies and middleware

## Proposed Solution

### Architecture Overview

```
┌─────────────────────────────────────────────────────────────┐
│                     Presentation Layer                       │
│  (Inertia/Vue Components: Sprint Board, Backlog Hierarchy)  │
└────────────────────┬────────────────────────────────────────┘
                     │
┌────────────────────┴────────────────────────────────────────┐
│                     Controller Layer                         │
│    (SprintController: start, close, addItems, metrics)      │
└────────────────────┬────────────────────────────────────────┘
                     │
┌────────────────────┴────────────────────────────────────────┐
│                     Service Layer                            │
│            SprintService (Business Logic Hub)                │
│  • calculatePoints()     • getBurndownData()                │
│  • startSprint()         • getVelocity()                    │
│  • closeSprint()         • createSnapshot()                 │
│  • logEvent()            • recalculatePoints()              │
└────────┬───────────────────────────────┬────────────────────┘
         │                               │
┌────────┴─────────────┐    ┌───────────┴──────────────────┐
│   Observer Layer      │    │      Model Layer             │
│                       │    │                              │
│ BacklogItemObserver   │───▶│  Sprint                      │
│ • created()           │    │  BacklogItem (w/ parent_id)  │
│ • updated()           │    │  SprintSnapshot              │
│ • deleted()           │    │  SprintEvent                 │
│                       │    │                              │
│ SprintObserver        │    │                              │
│ • updating()          │    │  Relationships:              │
│ • deleting()          │    │  • BacklogItem → parent      │
│   (immutability)      │    │  • BacklogItem → children    │
└───────────────────────┘    │  • Sprint → snapshots        │
                              │  • Sprint → events           │
                              └──────────────────────────────┘
```

### Data Model Changes

#### 1. BacklogItem Enhancements
**Add to existing table:**
- `parent_id` (nullable, foreign key to backlog_items.id)
- Composite index on (project_id, parent_id, rank)
- Self-referencing foreign key constraint

**Modified ranking:**
- Rank is now scoped to parent (not global)
- Items without parent have global rank within project
- Items with parent have rank within that parent

#### 2. New Table: sprint_snapshots
```sql
CREATE TABLE sprint_snapshots (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    sprint_id BIGINT NOT NULL,
    snapshot_date DATE NOT NULL,
    remaining_points INT NOT NULL DEFAULT 0,
    completed_points INT NOT NULL DEFAULT 0,
    planned_points INT NOT NULL DEFAULT 0,
    scope_change INT NOT NULL DEFAULT 0, -- points added/removed that day
    created_at TIMESTAMP,
    
    FOREIGN KEY (sprint_id) REFERENCES sprints(id) ON DELETE CASCADE,
    UNIQUE KEY unique_sprint_date (sprint_id, snapshot_date),
    INDEX idx_sprint_date (sprint_id, snapshot_date)
);
```

**Purpose:** Store daily snapshots for accurate historical burndown charts

#### 3. New Table: sprint_events
```sql
CREATE TABLE sprint_events (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    sprint_id BIGINT NOT NULL,
    event_type VARCHAR(50) NOT NULL,
    user_id BIGINT NULL,
    metadata JSON NULL,
    created_at TIMESTAMP,
    
    FOREIGN KEY (sprint_id) REFERENCES sprints(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_sprint_created (sprint_id, created_at),
    INDEX idx_event_type (event_type)
);
```

**Event Types:**
- `sprint_started` - Sprint moved from planned to active
- `sprint_closed` - Sprint moved from active to completed
- `item_added` - Backlog item assigned to sprint
- `item_removed` - Backlog item removed from sprint
- `estimate_changed` - Backlog item points modified
- `scope_changed` - Items added/removed mid-sprint
- `status_updated` - Sprint status changed

**Metadata Examples:**
```json
// item_added event
{
  "backlog_item_id": 123,
  "title": "User login feature",
  "points": 5,
  "type": "story"
}

// estimate_changed event
{
  "backlog_item_id": 123,
  "old_points": 5,
  "new_points": 8,
  "reason": "Complexity underestimated"
}
```

#### 4. Sprint Model Changes
**Remove from fillable:**
- `planned_points` (will be computed property)
- `completed_points` (will be computed property)

**Add accessors:**
```php
protected function plannedPoints(): Attribute
{
    return Attribute::make(
        get: fn() => $this->backlogItems()->sum('points')
    );
}

protected function completedPoints(): Attribute
{
    return Attribute::make(
        get: fn() => $this->backlogItems()
            ->where('status', 'done')
            ->sum('points')
    );
}
```

**Add database constraints:**
```sql
-- Prevent multiple active sprints per project
CREATE UNIQUE INDEX idx_one_active_sprint_per_project 
ON sprints(project_id) 
WHERE status = 'active' AND deleted_at IS NULL;

-- Trigger to prevent updates to completed sprints
CREATE TRIGGER prevent_completed_sprint_updates
BEFORE UPDATE ON sprints
FOR EACH ROW
BEGIN
    IF OLD.status = 'completed' THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Cannot modify completed sprint';
    END IF;
END;
```

### Service Layer Design

#### SprintService

**Location:** `app/Services/SprintService.php`

**Dependencies:**
- `Sprint` model
- `BacklogItem` model
- `SprintSnapshot` model
- `SprintEvent` model
- `Cache` facade
- `DB` facade

**Public Methods:**

```php
class SprintService
{
    // Point Calculation
    public function calculatePlannedPoints(Sprint $sprint): int
    public function calculateCompletedPoints(Sprint $sprint): int
    public function recalculateSprintPoints(Sprint $sprint): void
    
    // Lifecycle Management
    public function canStartSprint(Sprint $sprint): bool
    public function validateSprintStart(Sprint $sprint): array // returns errors
    public function startSprint(Sprint $sprint): void
    public function closeSprint(Sprint $sprint): void
    public function autoCloseExpiredSprints(Project $project): int
    
    // Item Management
    public function addBacklogItems(Sprint $sprint, array $itemIds): void
    public function removeBacklogItem(Sprint $sprint, BacklogItem $item): void
    public function moveItemBetweenSprints(BacklogItem $item, Sprint $from, Sprint $to): void
    
    // Metrics & Analytics
    public function getVelocity(Project $project, int $sprintCount = 3): array
    public function getBurndownData(Sprint $sprint): array
    public function getSprintMetrics(Sprint $sprint): array
    
    // Snapshots
    public function createDailySnapshot(Sprint $sprint): SprintSnapshot
    public function createSnapshotsForDateRange(Sprint $sprint, Carbon $start, Carbon $end): Collection
    
    // Events & Audit
    public function logEvent(Sprint $sprint, string $eventType, array $metadata = []): SprintEvent
    public function getSprintHistory(Sprint $sprint): Collection
}
```

**Implementation Pattern:**
- Follow existing `ProjectProgressService` pattern
- Use database transactions for multi-step operations
- Cache expensive calculations (velocity, burndown for completed sprints)
- Log all significant actions
- Throw descriptive exceptions for validation failures

### Observer Implementation

#### BacklogItemObserver

**Location:** `app/Observers/BacklogItemObserver.php`

**Responsibilities:**
- Trigger sprint point recalculation when items change
- Log sprint events when items are added/removed from sprints
- Validate parent/project consistency
- Prevent circular parent references

**Implementation:**
```php
class BacklogItemObserver
{
    public function __construct(
        protected SprintService $sprintService
    ) {}
    
    public function creating(BacklogItem $item): void
    {
        // Validate parent belongs to same project
        // Auto-assign rank if not provided
    }
    
    public function created(BacklogItem $item): void
    {
        if ($item->sprint_id) {
            $this->sprintService->recalculateSprintPoints($item->sprint);
            $this->sprintService->logEvent(
                $item->sprint,
                'item_added',
                ['backlog_item_id' => $item->id, 'points' => $item->points]
            );
        }
    }
    
    public function updating(BacklogItem $item): void
    {
        // Validate parent change doesn't create circular reference
        // Validate sprint change doesn't violate project consistency
    }
    
    public function updated(BacklogItem $item): void
    {
        $sprintsToUpdate = [];
        
        // If sprint changed, update both old and new sprint
        if ($item->wasChanged('sprint_id')) {
            if ($item->getOriginal('sprint_id')) {
                $sprintsToUpdate[] = Sprint::find($item->getOriginal('sprint_id'));
            }
            if ($item->sprint_id) {
                $sprintsToUpdate[] = $item->sprint;
            }
        }
        // If points or status changed, update current sprint
        elseif ($item->wasChanged(['points', 'status']) && $item->sprint_id) {
            $sprintsToUpdate[] = $item->sprint;
        }
        
        foreach ($sprintsToUpdate as $sprint) {
            if ($sprint) {
                $this->sprintService->recalculateSprintPoints($sprint);
            }
        }
        
        // Log significant changes
        if ($item->wasChanged('points') && $item->sprint_id) {
            $this->sprintService->logEvent(
                $item->sprint,
                'estimate_changed',
                [
                    'backlog_item_id' => $item->id,
                    'old_points' => $item->getOriginal('points'),
                    'new_points' => $item->points
                ]
            );
        }
    }
    
    public function deleted(BacklogItem $item): void
    {
        if ($item->sprint_id) {
            $sprint = $item->sprint;
            $this->sprintService->recalculateSprintPoints($sprint);
            $this->sprintService->logEvent(
                $sprint,
                'item_removed',
                ['backlog_item_id' => $item->id, 'points' => $item->points]
            );
        }
    }
    
    public function restored(BacklogItem $item): void
    {
        if ($item->sprint_id) {
            $this->sprintService->recalculateSprintPoints($item->sprint);
        }
    }
}
```

#### SprintObserver

**Location:** `app/Observers/SprintObserver.php`

**Responsibilities:**
- Enforce sprint immutability for completed sprints
- Log sprint lifecycle events
- Prevent deletion of completed sprints
- Clear caches when sprints change

**Implementation:**
```php
class SprintObserver
{
    public function __construct(
        protected SprintService $sprintService
    ) {}
    
    public function updating(Sprint $sprint): void
    {
        // Prevent ANY modification to completed sprints
        if ($sprint->getOriginal('status') === 'completed') {
            throw new \RuntimeException(
                'Cannot modify completed sprint. Sprint data is immutable for historical accuracy.'
            );
        }
    }
    
    public function updated(Sprint $sprint): void
    {
        // Log status changes
        if ($sprint->wasChanged('status')) {
            $eventType = match($sprint->status) {
                'active' => 'sprint_started',
                'completed' => 'sprint_closed',
                default => 'status_updated'
            };
            
            $this->sprintService->logEvent($sprint, $eventType, [
                'old_status' => $sprint->getOriginal('status'),
                'new_status' => $sprint->status
            ]);
        }
        
        // Clear velocity cache for project when sprint completes
        if ($sprint->wasChanged('status') && $sprint->status === 'completed') {
            Cache::forget("project_velocity_{$sprint->project_id}");
        }
    }
    
    public function deleting(Sprint $sprint): void
    {
        // Prevent soft-delete of completed sprints
        if ($sprint->status === 'completed') {
            throw new \RuntimeException(
                'Cannot delete completed sprint. Historical data must be preserved.'
            );
        }
    }
}
```

### API Enhancements

#### New Routes

**Location:** `routes/web.php`

```php
// Sprint lifecycle actions
Route::post('/sprints/{sprint}/start', [SprintController::class, 'start'])
    ->name('sprints.start')
    ->middleware('can:sprint.manage');
    
Route::post('/sprints/{sprint}/close', [SprintController::class, 'close'])
    ->name('sprints.close')
    ->middleware('can:sprint.manage');

// Sprint backlog management
Route::post('/sprints/{sprint}/backlog-items', [SprintController::class, 'addBacklogItems'])
    ->name('sprints.backlog-items.add')
    ->middleware('can:sprint.manage');
    
Route::delete('/sprints/{sprint}/backlog-items/{backlogItem}', [SprintController::class, 'removeBacklogItem'])
    ->name('sprints.backlog-items.remove')
    ->middleware('can:sprint.manage');

// Sprint metrics
Route::get('/sprints/{sprint}/burndown', [SprintController::class, 'burndown'])
    ->name('sprints.burndown')
    ->middleware('can:agile.view');
    
Route::get('/sprints/{sprint}/velocity', [SprintController::class, 'velocity'])
    ->name('sprints.velocity')
    ->middleware('can:agile.view');
    
Route::get('/sprints/{sprint}/events', [SprintController::class, 'events'])
    ->name('sprints.events')
    ->middleware('can:agile.view');

// Backlog hierarchy
Route::patch('/backlog-items/{backlogItem}/parent', [BacklogItemController::class, 'updateParent'])
    ->name('backlog-items.update-parent')
    ->middleware('can:agile.view');
    
Route::patch('/backlog-items/{backlogItem}/rank', [BacklogItemController::class, 'updateRank'])
    ->name('backlog-items.update-rank')
    ->middleware('can:agile.view');
```

#### Controller Methods

**SprintController Additions:**

```php
public function start(Sprint $sprint): JsonResponse
{
    try {
        $this->sprintService->startSprint($sprint);
        return response()->json([
            'success' => true,
            'message' => 'Sprint started successfully',
            'sprint' => $sprint->fresh()->toInertia()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 422);
    }
}

public function close(Sprint $sprint): JsonResponse
{
    try {
        $this->sprintService->closeSprint($sprint);
        return response()->json([
            'success' => true,
            'message' => 'Sprint closed successfully',
            'sprint' => $sprint->fresh()->toInertia()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 422);
    }
}

public function addBacklogItems(Request $request, Sprint $sprint): JsonResponse
{
    $validated = $request->validate([
        'backlog_item_ids' => ['required', 'array'],
        'backlog_item_ids.*' => ['required', 'integer', 'exists:backlog_items,id']
    ]);
    
    try {
        $this->sprintService->addBacklogItems($sprint, $validated['backlog_item_ids']);
        return response()->json([
            'success' => true,
            'message' => 'Items added to sprint',
            'sprint' => $sprint->fresh()->toInertia()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 422);
    }
}

public function removeBacklogItem(Sprint $sprint, BacklogItem $backlogItem): JsonResponse
{
    try {
        $this->sprintService->removeBacklogItem($sprint, $backlogItem);
        return response()->json([
            'success' => true,
            'message' => 'Item removed from sprint'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 422);
    }
}

public function burndown(Sprint $sprint): JsonResponse
{
    $burndownData = $this->sprintService->getBurndownData($sprint);
    
    return response()->json([
        'success' => true,
        'data' => $burndownData
    ]);
}

public function velocity(Sprint $sprint): JsonResponse
{
    $velocityData = $this->sprintService->getVelocity($sprint->project);
    
    return response()->json([
        'success' => true,
        'data' => $velocityData
    ]);
}

public function events(Sprint $sprint): JsonResponse
{
    $events = $this->sprintService->getSprintHistory($sprint);
    
    return response()->json([
        'success' => true,
        'data' => $events
    ]);
}
```

### Frontend Components

#### 1. BacklogHierarchyTree.vue

**Location:** `resources/js/Components/Agile/BacklogHierarchyTree.vue`

**Features:**
- Tree structure display with expand/collapse
- Drag-and-drop to reorder and change parent
- Item type icons (epic, feature, story, bug, spike)
- Inline quick-add buttons
- Points and status badges
- Context menu for actions

**Props:**
```typescript
interface Props {
    items: BacklogItem[]
    projectId: number
    editable?: boolean
}

interface BacklogItem {
    id: number
    title: string
    type: 'epic' | 'feature' | 'story' | 'bug' | 'spike'
    points: number
    status: string
    priority: string
    parentId: number | null
    children?: BacklogItem[]
}
```

**Key Methods:**
- `buildHierarchy(items)` - Convert flat array to tree structure
- `handleDragStart(item)` - Begin drag operation
- `handleDrop(targetItem)` - Update parent and rank
- `expandAll()` / `collapseAll()` - Bulk expand/collapse
- `addChild(parentItem)` - Open quick-add modal

#### 2. SprintBoard.vue

**Location:** `resources/js/Pages/Agile/SprintBoard.vue`

**Features:**
- Sprint metrics card (planned, completed, progress)
- Burndown chart visualization
- Backlog items grouped by hierarchy
- Drag-and-drop to assign items to sprint
- Start/close sprint action buttons
- Real-time progress updates

**Components Used:**
- `BurndownChart.vue` - Chart.js/ApexCharts burndown visualization
- `SprintMetricsCard.vue` - Key metrics display
- `BacklogItemCard.vue` - Individual item display
- `SprintActions.vue` - Start/close buttons with validation

#### 3. QuickAddBacklogItem.vue

**Location:** `resources/js/Components/Agile/QuickAddBacklogItem.vue`

**Features:**
- Modal dialog for quick item creation
- Pre-filled project_id and parent_id
- Type dropdown filtered by valid child types
- Simple form (title, type, points)
- Inertia form submission

### Scheduled Tasks

#### Daily Sprint Snapshot Command

**Location:** `app/Console/Commands/CreateSprintSnapshots.php`

```php
<?php

namespace App\Console\Commands;

use App\Models\Sprint;
use App\Services\SprintService;
use Illuminate\Console\Command;

class CreateSprintSnapshots extends Command
{
    protected $signature = 'sprints:create-snapshots';
    protected $description = 'Create daily snapshots for all active sprints';

    public function __construct(protected SprintService $sprintService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $activeSprints = Sprint::where('status', 'active')->get();
        
        if ($activeSprints->isEmpty()) {
            $this->info('No active sprints found.');
            return Command::SUCCESS;
        }
        
        $created = 0;
        
        foreach ($activeSprints as $sprint) {
            try {
                $this->sprintService->createDailySnapshot($sprint);
                $created++;
                $this->info("Created snapshot for sprint: {$sprint->name}");
            } catch (\Exception $e) {
                $this->error("Failed to create snapshot for sprint {$sprint->id}: {$e->getMessage()}");
            }
        }
        
        $this->info("Created {$created} snapshots successfully.");
        
        return Command::SUCCESS;
    }
}
```

**Scheduler Registration:**

**Location:** `app/Console/Kernel.php`

```php
protected function schedule(Schedule $schedule): void
{
    // Create daily sprint snapshots at midnight
    $schedule->command('sprints:create-snapshots')
        ->daily()
        ->at('00:00');
}
```

## Implementation Task Breakdown

### Phase 1: Database & Models (Tasks 1-2)
**Duration:** 1-2 days

#### Task 1: Database Schema - Add Hierarchy and Snapshots
**Files to Create:**
- `database/migrations/YYYY_MM_DD_HHMMSS_add_parent_id_to_backlog_items.php`
- `database/migrations/YYYY_MM_DD_HHMMSS_create_sprint_snapshots_table.php`
- `database/migrations/YYYY_MM_DD_HHMMSS_create_sprint_events_table.php`
- `database/migrations/YYYY_MM_DD_HHMMSS_add_sprint_constraints.php`

**Implementation Details:**
```php
// Migration 1: Add parent_id
public function up(): void
{
    Schema::table('backlog_items', function (Blueprint $table) {
        $table->foreignId('parent_id')
            ->nullable()
            ->after('project_id')
            ->constrained('backlog_items')
            ->nullOnDelete();
        
        $table->index(['project_id', 'parent_id', 'rank']);
    });
}

// Migration 2: Sprint snapshots
public function up(): void
{
    Schema::create('sprint_snapshots', function (Blueprint $table) {
        $table->id();
        $table->foreignId('sprint_id')->constrained()->cascadeOnDelete();
        $table->date('snapshot_date');
        $table->unsignedInteger('remaining_points')->default(0);
        $table->unsignedInteger('completed_points')->default(0);
        $table->unsignedInteger('planned_points')->default(0);
        $table->integer('scope_change')->default(0);
        $table->timestamps();
        
        $table->unique(['sprint_id', 'snapshot_date']);
        $table->index(['sprint_id', 'snapshot_date']);
    });
}

// Migration 3: Sprint events
public function up(): void
{
    Schema::create('sprint_events', function (Blueprint $table) {
        $table->id();
        $table->foreignId('sprint_id')->constrained()->cascadeOnDelete();
        $table->string('event_type', 50);
        $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
        $table->json('metadata')->nullable();
        $table->timestamp('created_at');
        
        $table->index(['sprint_id', 'created_at']);
        $table->index('event_type');
    });
}

// Migration 4: Sprint constraints
public function up(): void
{
    // Note: SQLite doesn't support partial indexes or triggers
    // MySQL/PostgreSQL implementation
    DB::statement('
        CREATE UNIQUE INDEX idx_one_active_sprint_per_project 
        ON sprints(project_id) 
        WHERE status = "active" AND deleted_at IS NULL
    ');
    
    // Create trigger to prevent updates to completed sprints
    DB::unprepared('
        CREATE TRIGGER prevent_completed_sprint_updates
        BEFORE UPDATE ON sprints
        FOR EACH ROW
        BEGIN
            IF OLD.status = "completed" THEN
                SIGNAL SQLSTATE "45000" 
                SET MESSAGE_TEXT = "Cannot modify completed sprint";
            END IF;
        END
    ');
}
```

**Testing:**
```php
// tests/Feature/SprintSchemaMigrationTest.php
public function test_can_add_parent_to_backlog_item(): void
{
    $parent = BacklogItem::factory()->create(['type' => 'epic']);
    $child = BacklogItem::factory()->create([
        'parent_id' => $parent->id,
        'project_id' => $parent->project_id,
        'type' => 'story'
    ]);
    
    $this->assertEquals($parent->id, $child->parent_id);
}

public function test_sprint_snapshot_can_be_created(): void
{
    $sprint = Sprint::factory()->create();
    
    $snapshot = SprintSnapshot::create([
        'sprint_id' => $sprint->id,
        'snapshot_date' => now()->toDateString(),
        'remaining_points' => 50,
        'completed_points' => 10,
        'planned_points' => 60
    ]);
    
    $this->assertDatabaseHas('sprint_snapshots', [
        'sprint_id' => $sprint->id,
        'remaining_points' => 50
    ]);
}
```

#### Task 2: Update Models - BacklogItem Hierarchy Relations
**Files to Modify:**
- `app/Models/BacklogItem.php`
- `app/Models/Sprint.php`

**Files to Create:**
- `app/Models/SprintSnapshot.php`
- `app/Models/SprintEvent.php`

**Implementation:**
```php
// BacklogItem.php additions
class BacklogItem extends Model
{
    protected $fillable = [
        // ... existing fields
        'parent_id',
    ];
    
    // Relationships
    public function parent(): BelongsTo
    {
        return $this->belongsTo(BacklogItem::class, 'parent_id');
    }
    
    public function children(): HasMany
    {
        return $this->hasMany(BacklogItem::class, 'parent_id');
    }
    
    // Recursive relationships
    public function ancestors(): Collection
    {
        $ancestors = collect();
        $item = $this;
        
        while ($item->parent) {
            $ancestors->push($item->parent);
            $item = $item->parent;
        }
        
        return $ancestors;
    }
    
    public function descendants(): Collection
    {
        return $this->children->flatMap(function ($child) {
            return collect([$child])->merge($child->descendants());
        });
    }
    
    // Scopes
    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }
    
    public function scopeWithinParent($query, ?int $parentId)
    {
        return $query->where('parent_id', $parentId);
    }
    
    // Boot method for validation
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($item) {
            // Prevent circular references
            if ($item->parent_id) {
                $parent = BacklogItem::find($item->parent_id);
                if ($parent && $parent->ancestors()->contains('id', $item->id)) {
                    throw new \RuntimeException('Circular parent reference detected');
                }
            }
        });
    }
}

// Sprint.php modifications
class Sprint extends Model
{
    // Remove from fillable
    protected $fillable = [
        'project_id',
        'name',
        'goal',
        'start_date',
        'end_date',
        'status',
        // REMOVED: 'planned_points', 'completed_points'
    ];
    
    // Add computed properties
    protected function plannedPoints(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->backlogItems()->sum('points')
        );
    }
    
    protected function completedPoints(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->backlogItems()
                ->where('status', 'done')
                ->sum('points')
        );
    }
    
    // New relationships
    public function snapshots(): HasMany
    {
        return $this->hasMany(SprintSnapshot::class);
    }
    
    public function events(): HasMany
    {
        return $this->hasMany(SprintEvent::class);
    }
}

// SprintSnapshot.php
class SprintSnapshot extends Model
{
    protected $fillable = [
        'sprint_id',
        'snapshot_date',
        'remaining_points',
        'completed_points',
        'planned_points',
        'scope_change',
    ];
    
    protected $casts = [
        'snapshot_date' => 'date',
    ];
    
    public function sprint(): BelongsTo
    {
        return $this->belongsTo(Sprint::class);
    }
}

// SprintEvent.php
class SprintEvent extends Model
{
    const UPDATED_AT = null; // Only created_at timestamp
    
    protected $fillable = [
        'sprint_id',
        'event_type',
        'user_id',
        'metadata',
    ];
    
    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];
    
    public function sprint(): BelongsTo
    {
        return $this->belongsTo(Sprint::class);
    }
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
```

**Testing:**
```php
public function test_backlog_item_parent_child_relationship(): void
{
    $parent = BacklogItem::factory()->create(['type' => 'epic']);
    $child = BacklogItem::factory()->create([
        'parent_id' => $parent->id,
        'type' => 'story'
    ]);
    
    $this->assertEquals($parent->id, $child->parent->id);
    $this->assertTrue($parent->children->contains($child));
}

public function test_prevents_circular_reference(): void
{
    $this->expectException(\RuntimeException::class);
    
    $itemA = BacklogItem::factory()->create();
    $itemB = BacklogItem::factory()->create(['parent_id' => $itemA->id]);
    
    // Try to make A a child of B (circular)
    $itemA->parent_id = $itemB->id;
    $itemA->save();
}

public function test_sprint_planned_points_are_computed(): void
{
    $sprint = Sprint::factory()->create();
    
    BacklogItem::factory()->count(3)->create([
        'sprint_id' => $sprint->id,
        'points' => 5,
        'project_id' => $sprint->project_id
    ]);
    
    $this->assertEquals(15, $sprint->fresh()->planned_points);
}
```

---

### Phase 2: Validation & Business Logic (Tasks 3-7)
**Duration:** 3-4 days

#### Task 3: Enhanced Validation - Cross-Project and Hierarchy Rules
#### Task 4: Create SprintService - Core Business Logic
#### Task 5: Sprint Lifecycle Methods - Start and Close
#### Task 6: Sprint Immutability - Database Constraints and Observers
#### Task 7: BacklogItemObserver - Automatic Point Recalculation

_(Details in subsequent sections due to length)_

---

### Phase 3: API & Controllers (Tasks 8)
**Duration:** 1-2 days

#### Task 8: Sprint API Endpoints - Start, Close, Manage Items

_(Details in subsequent sections)_

---

### Phase 4: Metrics & Analytics (Tasks 9-12)
**Duration:** 2-3 days

#### Task 9: Velocity Calculation - Historical Sprint Metrics
#### Task 10: Daily Sprint Snapshots - Burndown Data Collection
#### Task 11: Burndown Chart Data - API and Calculations
#### Task 12: Sprint Event Logging - Audit Trail

_(Details in subsequent sections)_

---

### Phase 5: Frontend Components (Tasks 13-15)
**Duration:** 3-4 days

#### Task 13: Backlog Hierarchy UI - Tree View Component
#### Task 14: Inline Backlog Item Creation - Quick Add Modal
#### Task 15: Sprint Board Enhancements - Show Hierarchy and Metrics

_(Details in subsequent sections)_

---

### Phase 6: Testing (Tasks 16-19)
**Duration:** 2-3 days

#### Task 16: Comprehensive Feature Tests - Sprint Lifecycle
#### Task 17: Comprehensive Feature Tests - Backlog Hierarchy
#### Task 18: Comprehensive Feature Tests - Automatic Calculations
#### Task 19: Comprehensive Feature Tests - Metrics and Burndown

_(Details in subsequent sections)_

---

### Phase 7: Documentation (Task 20)
**Duration:** 1 day

#### Task 20: Documentation and Migration Guide

---

## Risk Assessment & Mitigation

### Risk 1: Data Migration Complexity
**Risk:** Existing sprints have manually entered planned_points/completed_points

**Impact:** High - Could lose historical data

**Mitigation:**
- Create migration script to backfill data before removing columns
- Store original values in metadata for audit
- Provide rollback migration
- Test on copy of production database first

### Risk 2: Performance Impact
**Risk:** Recursive queries and frequent observer triggers could slow down system

**Impact:** Medium - User experience degradation

**Mitigation:**
- Add database indexes on all foreign keys and query paths
- Cache expensive calculations (velocity, burndown for completed sprints)
- Use query scopes efficiently
- Add performance tests with large datasets
- Consider queue jobs for non-critical calculations

### Risk 3: Observer Recursion
**Risk:** Observers triggering observers could create infinite loops

**Impact:** High - Application crash

**Mitigation:**
- Use flags to prevent recursion (e.g., `$item->withoutEvents()`)
- Limit observer actions to necessary calculations only
- Add recursion detection in critical paths
- Comprehensive testing of observer chains

### Risk 4: Breaking Changes
**Risk:** Removing editable planned_points/completed_points breaks existing workflows

**Impact:** Medium - User workflow disruption

**Mitigation:**
- Provide clear migration guide
- Add deprecation warnings before removal
- Ensure new automatic calculations match old manual values
- Provide admin override if needed (via direct SQL for emergencies)

### Risk 5: Cross-Project Data Integrity
**Risk:** Existing data may violate new constraints

**Impact:** High - Migration failure

**Mitigation:**
- Run validation queries before applying constraints
- Create cleanup script to fix invalid data
- Log all cleanup actions for audit
- Allow manual resolution of edge cases

## Testing Strategy

### Unit Tests
**Focus:** Individual methods and calculations

**Coverage:**
- SprintService methods (point calculations, validation logic)
- Model relationships and scopes
- Custom validation rules
- Helper methods (ancestors, descendants)

**Target:** 90%+ code coverage on business logic

### Feature Tests
**Focus:** End-to-end workflows

**Coverage:**
- Sprint lifecycle (create → start → work → close)
- Backlog hierarchy operations
- Automatic point recalculation
- Cross-project validation
- Immutability enforcement
- Metrics calculation
- API endpoint responses

**Target:** All critical user paths covered

### Integration Tests
**Focus:** Observer and service interactions

**Coverage:**
- Observer triggering and cascading
- Service layer coordination
- Event logging
- Cache invalidation
- Database constraint enforcement

### Performance Tests
**Focus:** System performance under load

**Coverage:**
- Large backlog hierarchies (1000+ items)
- Multiple concurrent sprint operations
- Burndown data generation for long sprints
- Velocity calculation with many historical sprints

**Target:** Operations complete within acceptable time limits

### Browser Tests (Optional)
**Focus:** Frontend functionality

**Coverage:**
- Drag-and-drop hierarchy reorganization
- Sprint board interactions
- Chart rendering
- Real-time updates

## Deployment Plan

### Pre-Deployment Checklist
- [ ] All tests passing (unit, feature, integration)
- [ ] Code review completed
- [ ] Database backup created
- [ ] Migration tested on staging environment
- [ ] Performance benchmarks meet requirements
- [ ] Documentation complete
- [ ] Rollback plan prepared

### Deployment Steps

1. **Database Backup**
   ```bash
   php artisan db:backup
   ```

2. **Enable Maintenance Mode**
   ```bash
   php artisan down --message="Upgrading Agile Module" --retry=60
   ```

3. **Pull Latest Code**
   ```bash
   git pull origin main
   ```

4. **Install Dependencies**
   ```bash
   composer install --no-dev --optimize-autoloader
   npm install
   npm run build
   ```

5. **Run Migrations**
   ```bash
   php artisan migrate --force
   ```

6. **Clear Caches**
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan view:clear
   ```

7. **Backfill Data (if needed)**
   ```bash
   php artisan sprints:backfill-snapshots
   ```

8. **Verify Deployment**
   ```bash
   php artisan sprints:verify-integrity
   ```

9. **Disable Maintenance Mode**
   ```bash
   php artisan up
   ```

10. **Monitor Logs**
   ```bash
   tail -f storage/logs/laravel.log
   ```

### Post-Deployment Monitoring

**First 24 Hours:**
- Monitor error logs for exceptions
- Check database query performance
- Verify observer behavior in production
- Monitor cache hit rates
- Verify snapshot creation runs successfully

**First Week:**
- Gather user feedback
- Monitor system performance metrics
- Verify historical data accuracy
- Check audit trail completeness

### Rollback Plan

If critical issues arise:

1. **Enable Maintenance Mode**
   ```bash
   php artisan down
   ```

2. **Revert Code**
   ```bash
   git revert HEAD
   git push origin main
   ```

3. **Rollback Database**
   ```bash
   php artisan migrate:rollback --step=4
   ```

4. **Restore Database Backup (if needed)**
   ```bash
   php artisan db:restore backup-file.sql
   ```

5. **Clear Caches**
   ```bash
   php artisan cache:clear
   php artisan config:clear
   ```

6. **Disable Maintenance Mode**
   ```bash
   php artisan up
   ```

## Success Metrics

### Technical Metrics
- All 50+ feature tests passing
- 90%+ code coverage on business logic
- API response times < 200ms for 95th percentile
- Observer-triggered calculations complete in < 100ms
- Zero database constraint violations in production

### User Experience Metrics
- Users can create multi-level backlog hierarchies
- Sprint points auto-update immediately when items change
- Burndown charts display accurately for all historical sprints
- No manual point entry required
- Completed sprints remain immutable

### Business Metrics
- 100% of teams able to track velocity
- Historical sprint data preserved accurately
- Audit trail provides complete sprint history
- Zero data integrity issues post-deployment

## Timeline Summary

| Phase | Duration | Tasks | Deliverables |
|-------|----------|-------|--------------|
| Phase 1: Database & Models | 1-2 days | 1-2 | Migrations, model relationships |
| Phase 2: Business Logic | 3-4 days | 3-7 | Validation, service, observers |
| Phase 3: API | 1-2 days | 8 | Controller methods, routes |
| Phase 4: Metrics | 2-3 days | 9-12 | Velocity, burndown, snapshots |
| Phase 5: Frontend | 3-4 days | 13-15 | Vue components, UI |
| Phase 6: Testing | 2-3 days | 16-19 | Comprehensive test suite |
| Phase 7: Documentation | 1 day | 20 | Guides and docs |

**Total Estimated Duration:** 13-19 days

## Conclusion

This implementation plan provides a comprehensive roadmap for enhancing the Agile Module with industry-standard features including hierarchical backlog management, automated calculations, proper lifecycle management, and advanced metrics.

The phased approach ensures:
- Incremental development with testable milestones
- Minimal disruption to existing functionality
- Clear rollback points at each phase
- Comprehensive testing before deployment
- Proper documentation for users and developers

Following this plan will result in a robust, scalable Agile Module that supports effective scrum/agile practices and provides accurate historical reporting.

---

**Document Version:** 1.0  
**Last Updated:** 2026-09-25  
**Author:** Kiro AI  
**Status:** Ready for Implementation
