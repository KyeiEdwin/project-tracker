# Database + Eloquent model integration (Laravel 12 + Inertia + Vue)

**Status (executed):** `Project` is `App\Models\Project`. Migration `2026_09_19_135749_create_projects_table` has been run on SQLite. Factory and `DatabaseSeeder` load five demo projects. List/create/show now persist through Eloquent. `php artisan make:model` first wrote `app/Project.php` because `app/Models` did not exist; the class was moved to `App\Models`. The live schema also stores `project_type`, `start_date`, `team`, `client`, `progress`, `spent`, and `settings` so the existing Vue create form can save without a second migration.

This workflow adds a new table and Eloquent model using **only built-in Artisan commands**. It is written for this Project Tracker app (Laravel 12, SQLite by default, Inertia + Vue already in place). It does **not** generate Vue pages or controllers.

**Honest constraint:** core Artisan cannot define custom columns. The efficient path is one generate command, one schema paste into the migration, then `migrate`. There is no extra step between generate and migrate besides filling the schema (provided below).

Example entity: **Project** — matches `ProjectController::store()` (`name`, `description`, `priority`, `status`, `dueDate`, `budget`).

---

## 0. Setup / verification (run once)

From the project root (`c:\Users\XPS\Herd\project-tracker`):

```powershell
php artisan --version
php artisan env
php artisan db:show
```

**Expect:** Laravel 12.x, `APP_ENV=local`, `DB_CONNECTION=sqlite`.

If `database/database.sqlite` is missing:

```powershell
New-Item -ItemType File -Force database/database.sqlite
```

Laravel 12 will also create the SQLite file on first `migrate` if the path in `.env` is valid.

Confirm you are not about to clobber production:

```powershell
php artisan about
```

**Expect:** Environment `local`, database `sqlite`.

---

## 1. Copy-paste command sequence

Run these in order in PowerShell from the project root.

### 1. Generate model + migration + factory (one command)

```powershell
php artisan make:model Project -mf --no-interaction
```

| Flag | Purpose |
|------|---------|
| `-m` / `--migration` | Creates `database/migrations/*_create_projects_table.php` |
| `-f` / `--factory` | Creates `database/factories/ProjectFactory.php` (seed/tests later; optional for Inertia) |
| `--no-interaction` | Skips prompts; safe in scripts |

**Do not use** `-c`, `-r`, or `-a` here — those generate HTTP layers you are not integrating in this pass.

**Expect:**

```
INFO  Model [app/Models/Project.php] created successfully.
INFO  Factory [database/factories/ProjectFactory.php] created successfully.
INFO  Migration [database/migrations/YYYY_MM_DD_HHMMSS_create_projects_table.php] created successfully.
```

**Generated:**

- `app/Models/Project.php`
- `database/factories/ProjectFactory.php`
- `database/migrations/YYYY_MM_DD_HHMMSS_create_projects_table.php`

### 2. Confirm files exist

```powershell
php artisan make:model Project --no-interaction
```

**Expect:** `ERROR  Model already exists.` — proof the class is on disk. Do not force overwrite.

List the new migration:

```powershell
Get-ChildItem database\migrations\*create_projects_table.php
```

### 3. Fill the migration schema (only manual edit)

Open the file Artisan just created and replace its `up()` / `down()` with the schema in [Sample migration](#sample-migration). Save the file.

Optional factory fill (not required to migrate) is in [Sample factory](#sample-factory).

### 4. Preview, then run the migration

```powershell
php artisan migrate:status
php artisan migrate --pretend
php artisan migrate --no-interaction
```

| Flag | Purpose |
|------|---------|
| `--pretend` | Prints SQL; does not write |
| `--no-interaction` | No confirm prompts |
| `--force` | Needed only in production; **do not use locally** |

**Expect (`migrate`):**

```
INFO  Running migrations.
YYYY_MM_DD_HHMMSS_create_projects_table ...................... 10ms DONE
```

### 5. Verify table + model

```powershell
php artisan migrate:status
php artisan db:table projects
php artisan model:show Project
```

**Expect:**

- `migrate:status` — `create_projects_table` is **Ran**
- `db:table projects` — columns `id`, `name`, `slug`, `description`, `priority`, `status`, `due_date`, `budget`, `owner_id`, `timestamps`
- `model:show Project` — table `projects`, attributes, and relationship stubs listed

Smoke-test Eloquent (type `exit` when done):

```powershell
php artisan tinker
```

```php
App\Models\Project::query()->create([
    'name' => 'Pilot',
    'priority' => 'medium',
    'status' => 'planning',
]);
App\Models\Project::query()->count(); // 1
App\Models\Project::query()->first()->toArray();
```

Inertia note: controllers can now `Project::query()->latest()->get()` and pass the collection to `Inertia::render(...)`. Vue does not need a new component for the table to be “integrated”; it only needs props when you wire a page.

---

## Numbered checklist (execute immediately)

1. `cd` to the Laravel project root.
2. `php artisan db:show` — SQLite (or your intended connection) is reachable.
3. `php artisan make:model Project -mf --no-interaction`
4. Confirm `app/Models/Project.php` and `database/migrations/*_create_projects_table.php` exist.
5. Paste the sample schema into the migration; paste the sample model into `app/Models/Project.php`.
6. `php artisan migrate --pretend` then `php artisan migrate --no-interaction`
7. `php artisan db:table projects` and `php artisan model:show Project`
8. Optional: `php artisan tinker` create + count as above.

---

## Repeatable one-liner (after schema is saved)

Once the migration file contains the schema, this is the full generate-and-migrate sequence for a **new** entity. Replace `Project` / `projects` as needed:

```powershell
php artisan make:model Project -mf --no-interaction; php artisan migrate --no-interaction; php artisan model:show Project
```

First time for Project: run `make:model`, **save schema + model**, then:

```powershell
php artisan migrate --no-interaction; php artisan db:table projects; php artisan model:show Project
```

---

## Adding a different table later

```powershell
php artisan make:model Task -mf --no-interaction
```

Edit `*_create_tasks_table.php`, then:

```powershell
php artisan migrate --no-interaction
php artisan model:show Task
```

Foreign keys: create the **parent** table first (`projects` before `tasks`). If you generate both before migrating, put the parent migration timestamp first (Artisan already does this if you generate Project, migrate, then Task).

---

## Sample migration

Replace the body of `database/migrations/YYYY_MM_DD_HHMMSS_create_projects_table.php` with:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            $table->string('priority', 32);
            $table->string('status', 32);

            $table->date('due_date')->nullable();
            $table->decimal('budget', 14, 2)->nullable();

            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index('status');
            $table->index('priority');
            $table->index('due_date');
            $table->index(['status', 'due_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
```

### Schema choices

| Column | Type | Why |
|--------|------|-----|
| `id` | bigint PK | Default Laravel primary key |
| `name` | string, required | Matches form validation |
| `slug` | unique string | Stable URLs / lookups; set on the model |
| `description` | nullable text | Optional long copy |
| `priority` / `status` | short strings | Indexed filters; keep in sync with Vue options |
| `due_date` | nullable date | Form `dueDate` (camelCase in JSON via model) |
| `budget` | nullable decimal(14,2) | Money without float error |
| `owner_id` | nullable FK | Optional; **omit `constrained('users')` if you have no `users` table** |
| `timestamps` | `created_at` / `updated_at` | Eloquent default |

**SQLite / this repo:** there is no `users` migration yet. For a first migrate that cannot fail, use this variant instead of `foreignId()->constrained()`:

```php
$table->unsignedBigInteger('owner_id')->nullable()->index();
```

Add `->constrained('users')->nullOnDelete()` in a later migration after `users` exists.

---

## Sample Eloquent model

Replace `app/Models/Project.php` with:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Project extends Model
{
    use HasFactory;

    // Omit $table; Eloquent uses "projects" from the class name.

    protected $fillable = [
        'name',
        'slug',
        'description',
        'priority',
        'status',
        'due_date',
        'budget',
        'owner_id',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'budget' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Project $project): void {
            if (blank($project->slug)) {
                $project->slug = Str::slug($project->name).'-'.Str::lower(Str::random(6));
            }
        });
    }

    /** Stub: generate Task with `php artisan make:model Task -mf` then point tasks.project_id here. */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}
```

If `Task` does not exist yet, comment out `tasks()` or generate Task before using that relation. A model without the relation still migrates and queries fine:

```php
// public function tasks(): HasMany
// {
//     return $this->hasMany(Task::class);
// }
```

Mass assignment: `$fillable` is explicit on purpose. Do not use `$guarded = []` unless every column is safe to mass-assign.

Inertia: attribute names are snake_case in the database. Vue can keep `dueDate` if you map in the controller, or send `due_date` and bind that prop. Either is valid; pick one per form.

---

## Sample factory

Optional. Replace the `definition()` in `database/factories/ProjectFactory.php`:

```php
<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->sentence(3);

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numerify('######'),
            'description' => fake()->optional()->paragraph(),
            'priority' => fake()->randomElement(['low', 'medium', 'high', 'critical']),
            'status' => fake()->randomElement(['planning', 'active', 'on_hold', 'completed']),
            'due_date' => fake()->optional()->date(),
            'budget' => fake()->optional()->randomFloat(2, 1000, 250000),
            'owner_id' => null,
        ];
    }
}
```

Create sample rows without a seeder class:

```powershell
php artisan tinker --execute="App\Models\Project::factory()->count(5)->create();"
```

---

## How to know it is integrated

| Check | Pass condition |
|-------|----------------|
| Files | `app/Models/Project.php`, matching create migration, optional factory |
| Autoload | `php artisan model:show Project` (no “class not found”) |
| Schema | `php artisan db:table projects` lists intended columns + indexes |
| Persist | Tinker `create()` + `count()` |
| Inertia-ready | A controller can `Project::query()->get()` and pass `'projects' => $projects` into `Inertia::render()` |
| Rollback | `php artisan migrate:rollback --step=1` drops `projects`; re-run `migrate` to restore |

Do **not** use `migrate:fresh` unless you accept dropping **all** tables.

---

## Rollback / mistakes

```powershell
php artisan migrate:rollback --step=1
```

If the migration failed mid-run on SQLite, fix the file and run `php artisan migrate` again. If the table was created empty/wrong:

```powershell
php artisan migrate:rollback --step=1
php artisan migrate
```

---

## What this workflow does not do

- No Vue SFCs, Pinia stores, or Inertia page props beyond noting how to pass the model.
- No third-party generators (`laravel-shift`, InfyOm, etc.).
- No custom Artisan commands.
- Controller/Form Request generation is out of scope (`make:controller` / `make:request` when you wire HTTP).
