# Implementation Plan - Transition Vue 3 SPA to Laravel + Inertia.js + Vue 3

Migrate the existing **Project Tracker** Vue 3 SPA (with 24 view pages, Xintra design system, and Tailwind CSS) into a full-featured **Laravel 12 + Inertia.js + Vue 3** application managed via Laravel Herd.

## User Review Required

> [!IMPORTANT]
> **Directory Structure Alignment**: 
> In Laravel + Inertia.js conventions, frontend source files move from `src/` to `resources/js/` and `resources/css/`. 
> - `src/views/` will be organized into `resources/js/Pages/`
> - `src/components/` into `resources/js/Components/` and `resources/js/Layouts/`
> - `src/assets/` will be migrated to `resources/css/`, `resources/js/`, and `public/` (for fonts & static images).
> - Client-side `vue-router` will be decommissioned; all routing and navigation will be powered by Laravel `routes/web.php` and Inertia's `<Link>` component.

> [!NOTE]
> `laravel/framework` and `inertiajs/inertia-laravel` are already downloaded in `vendor/`. We will scaffold the required Laravel application structure (`artisan`, `bootstrap/app.php`, `public/index.php`, `app/Http/Middleware/HandleInertiaRequests.php`, `routes/web.php`) and configure Herd compatibility seamlessly.

---

## Proposed Changes

### Phase 1: Laravel Backend Scaffolding & Inertia Middleware

Scaffold the minimal, modern Laravel 12 application boilerplate to make Herd serve the application:

#### [NEW] `artisan`
#### [NEW] `bootstrap/app.php`
- Configure Laravel routing, middleware, and exception handling.
- Register `HandleInertiaRequests` middleware into the `web` middleware group.

#### [NEW] `public/index.php`
- Standard front controller entry point for Laravel Herd / Nginx.

#### [NEW] `.env` & `.env.example`
- Configure `APP_NAME="Project Tracker"`, `APP_ENV=local`, `APP_KEY`, `APP_URL=http://project-tracker.test`.

#### [NEW] `app/Http/Middleware/HandleInertiaRequests.php`
- Inherit `Inertia\Middleware`.
- Define the root template (`app.blade.php`).
- Pass shared props (auth user, flash notifications, app metadata).

#### [NEW] `resources/views/app.blade.php`
- The HTML root layout containing fonts, `@vite(['resources/css/app.css', 'resources/js/app.js'])`, `@inertiaHead`, and `@inertia`.

---

### Phase 2: Route Architecture & Controllers (`routes/web.php`)

Define all backend routes matching the existing 24 views, returning `Inertia::render()` with structured data:

#### [NEW] `routes/web.php`

```php
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\InitiationController;
use App\Http\Controllers\AgileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\QualityController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ChatController;

// Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Projects
Route::prefix('projects')->name('projects.')->group(function () {
    Route::get('/', [ProjectController::class, 'index'])->name('index');
    Route::get('/create', [ProjectController::class, 'create'])->name('create');
    Route::post('/', [ProjectController::class, 'store'])->name('store');
    Route::get('/{id}', [ProjectController::class, 'show'])->name('show');
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
```

#### [NEW] Controllers under `app/Http/Controllers/`:
- `DashboardController.php`
- `ProjectController.php`
- `InitiationController.php`
- `AgileController.php`
- `TaskController.php`
- `ResourceController.php`
- `QualityController.php`
- `ReportController.php`
- `ChatController.php`

---

### Phase 3: Frontend Reorganization & Inertia Setup

#### [MODIFY] `package.json`
- Install `@inertiajs/vue3`, `laravel-vite-plugin`.
- Ensure Vue 3, Tailwind CSS, PostCSS, and Pinia are configured.

#### [MODIFY] `vite.config.js`
- Configure `laravel-vite-plugin`:
```javascript
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: {
            '@': '/resources/js',
        },
    },
});
```

#### [NEW] `resources/js/app.js`
- Inertia client bootstrap using `createInertiaApp`:
```javascript
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createPinia } from 'pinia';
import '../css/app.css';

createInertiaApp({
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });
        app.use(plugin);
        app.use(createPinia());
        app.mount(el);
    },
});
```

#### [NEW] `resources/js/Layouts/AppLayout.vue`
- Converted from `src/App.vue`.
- Wraps `AppHeader`, `AppSidebar`, and `AppFooter`.
- Slots `<slot />` where `<router-view />` was.

#### [NEW] `resources/js/Components/`
- Migrate layout components (`AppHeader.vue`, `AppSidebar.vue`, `AppFooter.vue`) and UI components (`PageHeader.vue`, `StatsCard.vue`).
- Replace `router-link` with Inertia `<Link :href="...">`.
- Replace `useRoute()` with `usePage()`.

#### [NEW] `resources/js/Pages/`
- Migrate all 24 Vue view components from `src/views/` into `resources/js/Pages/`:
  - `Pages/Dashboard.vue`
  - `Pages/Projects/Index.vue`, `Pages/Projects/Create.vue`, `Pages/Projects/Show.vue`
  - `Pages/Initiation/Kickoff.vue`, `Pages/Initiation/Stakeholders.vue`
  - `Pages/Agile/Sprints.vue`, `Pages/Agile/Backlog.vue`, `Pages/Agile/Definitions.vue`
  - `Pages/Tasks/Index.vue`, `Pages/Tasks/Kanban.vue`, `Pages/Tasks/Workflows.vue`
  - `Pages/Resources/Team.vue`, `Pages/Resources/TimeTracking.vue`, `Pages/Resources/Budget.vue`, `Pages/Resources/Milestones.vue`, `Pages/Resources/Gantt.vue`
  - `Pages/Quality/QaTesting.vue`, `Pages/Quality/Risks.vue`, `Pages/Quality/ChangeLog.vue`
  - `Pages/Reports/Analytics.vue`, `Pages/Reports/Documents.vue`, `Pages/Reports/LessonsLearned.vue`
  - `Pages/Communication/Chat.vue`
- Wrap pages with `<AppLayout>` (or set persistent layout).
- Replace internal `router.push()` or `router-link` calls with Inertia `router.visit()` or `<Link>`.

#### [NEW] `resources/css/app.css` & Assets
- Import `styles.css`, `pm-custom.css`, and icon fonts.
- Copy font assets (`icon-fonts/`) and static images (`images/`) to `public/` so web font URLs and images resolve properly.

---

### Phase 4: Cleanup & Transition

- Remove deprecated `src/` and `index.html` after verifying all components work under `resources/js/`.
- Decommission `vue-router` from `package.json`.

---

## Verification Plan

### Automated Tests & CLI Checks
1. **Artisan Check**:
   - `php artisan --version`
   - `php artisan route:list` to verify all 24 routes are registered cleanly.
2. **Vite Compilation**:
   - `npm run build` to confirm Vite bundles all CSS, fonts, and Inertia Vue pages without missing imports.

### Manual Verification
1. **Herd / Dev Server Execution**:
   - Run `npm run dev` and navigate to the local Herd host (e.g. `http://project-tracker.test` or `php artisan serve`).
2. **Inertia SPA Navigation**:
   - Verify sidebar navigation across Dashboard, Projects, Agile, Tasks, Resources, Quality, Reports, and Chat.
   - Confirm page transitions happen via Inertia XHR without full page reload.
   - Verify dropdowns, theme toggles, and layout styling render identically to the Vue SPA.
