# Project Tracker

A full-stack project management application for planning, executing, and reporting on work across the project lifecycle. The backend is **Laravel 12**; the frontend is **Vue 3** delivered through **Inertia.js**, with **Vite**, **Tailwind CSS**, and **Pinia**.

New contributors can clone the repo, install PHP and Node dependencies, copy the environment file, and run the Laravel server plus Vite together.

---

## Key features

- **Dashboard** — High-level view of projects, tasks, and key metrics
- **Projects** — Create, list, and inspect projects (name, priority, status, due date, budget)
- **Initiation** — Kickoff planning and stakeholder management
- **Agile** — Sprint planning, product backlog, and Definition of Ready / Definition of Done
- **Tasks** — Task lists, Kanban boards, and workflow configuration
- **Resources** — Team roster, time tracking, budget, milestones, and Gantt charts
- **Quality** — QA testing, risk register, and change log
- **Reports** — Analytics, document library, and lessons learned
- **Chat** — In-app team communication
- **Theming** — Light and dark layout styles via the Xintra-based UI

---

## Tech stack

| Layer | Technology |
|-------|------------|
| Backend | PHP 8.2+, Laravel 12 |
| SPA bridge | Inertia.js (Laravel + Vue 3 adapters) |
| Frontend | Vue 3, Pinia, VueUse, ApexCharts |
| Build | Vite 5, laravel-vite-plugin, PostCSS, Tailwind CSS 3 |
| Default database | SQLite (`DB_CONNECTION=sqlite`) |

---

## Prerequisites

Install these before setup:

- **PHP 8.2 or newer** with common Laravel extensions (OpenSSL, PDO, Mbstring, Tokenizer, XML, Ctype, JSON, BCMath, Fileinfo)
- **Composer 2**
- **Node.js 18+** and **npm** (or a compatible Node package manager)
- A web server or Laravel’s built-in server (`php artisan serve`)
- Optional: **Laravel Herd**, Valet, or Docker if you prefer those local stacks

---

## Installation

1. **Clone the repository**

   ```bash
   git clone <repository-url> project-tracker
   cd project-tracker
   ```

2. **Install PHP dependencies**

   ```bash
   composer install
   ```

3. **Install frontend dependencies**

   ```bash
   npm install
   ```

4. **Configure the environment**

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

   Default `.env` uses SQLite and file-based cache, sessions, and queues. Set `APP_URL` to match how you serve the app (for example `http://project-tracker.test` with Herd, or `http://127.0.0.1:8000` with `artisan serve`).

5. **Prepare storage and the database**

   ```bash
   php artisan storage:link
   ```

   If you keep SQLite, create the database file when you add migrations:

   ```bash
   touch database/database.sqlite
   php artisan migrate
   ```

   There are currently no application migrations in this repository; run `migrate` once they are added.

6. **Run the app (two processes in development)**

   Terminal 1 — Laravel:

   ```bash
   php artisan serve
   ```

   Terminal 2 — Vite:

   ```bash
   npm run dev
   ```

   Open the URL printed by `artisan serve`, or your Herd/Valet hostname.

### Production build

Compile frontend assets, then serve Laravel as you normally would (Nginx/Apache/Herd, PHP-FPM, etc.):

```bash
npm run build
```

Do not commit compiled output (`public/build`, `dist`). Generate it in CI or on the server.

---

## Usage

Routes are defined in `routes/web.php` and rendered as Inertia Vue pages under `resources/js/Pages`.

| Path | Name | Description |
|------|------|-------------|
| `/` | `dashboard` | Main dashboard |
| `/projects` | `projects.index` | Project list |
| `/projects/create` | `projects.create` | Create project |
| `POST /projects` | `projects.store` | Persist a new project |
| `/projects/{id}` | `projects.show` | Project details |
| `/initiation/kickoff` | `initiation.kickoff` | Kickoff |
| `/initiation/stakeholders` | `initiation.stakeholders` | Stakeholders |
| `/agile/sprints` | `agile.sprints` | Sprints |
| `/agile/backlog` | `agile.backlog` | Backlog |
| `/agile/definitions` | `agile.definitions` | DoR / DoD |
| `/tasks` | `tasks.index` | Task list |
| `/tasks/kanban` | `tasks.kanban` | Kanban |
| `/tasks/workflows` | `tasks.workflows` | Workflows |
| `/resources/team` | `resources.team` | Team |
| `/resources/time-tracking` | `resources.time-tracking` | Time tracking |
| `/resources/budget` | `resources.budget` | Budget |
| `/resources/milestones` | `resources.milestones` | Milestones |
| `/resources/gantt` | `resources.gantt` | Gantt |
| `/quality/qa-testing` | `quality.qa-testing` | QA testing |
| `/quality/risks` | `quality.risks` | Risks |
| `/quality/change-log` | `quality.change-log` | Change log |
| `/reports/analytics` | `reports.analytics` | Analytics |
| `/reports/documents` | `reports.documents` | Documents |
| `/reports/lessons-learned` | `reports.lessons-learned` | Lessons learned |
| `/chat` | `chat` | Team chat |

Inertia page names match Vue files, for example `Inertia::render('Projects/Index')` → `resources/js/Pages/Projects/Index.vue`.

---

## Configuration

Primary settings live in `.env` (never commit real secrets). `.env.example` documents the expected keys.

| Variable | Purpose |
|----------|---------|
| `APP_NAME` | Application title (also exposed to Vite as `VITE_APP_NAME`) |
| `APP_ENV` / `APP_DEBUG` | Environment and debug output |
| `APP_KEY` | Encryption key (`php artisan key:generate`) |
| `APP_URL` | Canonical URL |
| `DB_CONNECTION` | Database driver (default `sqlite`) |
| `SESSION_DRIVER` | Session store (default `file`) |
| `CACHE_STORE` | Cache store (default `file`) |
| `QUEUE_CONNECTION` | Queue driver (default `sync`) |
| `LOG_CHANNEL` / `LOG_LEVEL` | Logging |

Laravel reads these values at runtime. Frontend aliases are defined in `vite.config.js` (`@` → `resources/js`).

Shared Inertia props are prepared in `app/Http/Middleware/HandleInertiaRequests.php`.

---

## Project structure

```
project-tracker/
├── app/                      # Laravel application code
│   ├── Http/Controllers/     # Inertia page controllers
│   ├── Http/Middleware/      # Inertia shared data, etc.
│   └── Providers/            # Service providers
├── bootstrap/                # Framework bootstrap and cached config
├── database/                 # Migrations, factories, seeders (as added)
├── public/                   # Web root (index.php, static assets, Vite build)
├── resources/
│   ├── css/                  # App CSS / Tailwind entry
│   ├── images/               # Frontend images
│   ├── js/
│   │   ├── Components/       # Layout and reusable Vue components
│   │   ├── Layouts/          # Inertia layouts (AppLayout)
│   │   ├── Pages/            # Inertia pages (one per screen)
│   │   └── app.js            # Vue + Inertia + Pinia bootstrap
│   └── views/                # Blade shell (app.blade.php)
├── routes/
│   ├── web.php               # HTTP routes
│   └── console.php           # Artisan console routes
├── storage/                  # Logs, cache, sessions, compiled views
├── tests/                    # Automated tests (add PHPUnit/Pest here)
├── artisan                   # Laravel CLI
├── composer.json             # PHP dependencies
├── package.json              # Node dependencies and Vite scripts
├── vite.config.js            # Vite + Vue + Laravel plugin
├── tailwind.config.js
└── postcss.config.js
```

`src/` and `dist/` may exist from an earlier standalone Vue build. The live application is **Laravel + `resources/js`**, not a separate `vue-app` directory.

---

## Development notes

### Adding a page

1. Add a Vue file under `resources/js/Pages/` (nested folders become the Inertia page name).
2. Add a controller action that returns `Inertia::render('Folder/PageName')`.
3. Register the route in `routes/web.php`.
4. Link it from `resources/js/Components/layout/AppSidebar.vue`.

### Frontend scripts

```bash
npm run dev      # Vite HMR during development
npm run build    # Production assets into public/build
```

### Artisan

```bash
php artisan route:list
php artisan config:clear
php artisan cache:clear
```

---

## Contributing

1. Create a branch from the default branch (`git checkout -b feature/short-description`).
2. Keep changes focused: one feature or fix per pull request.
3. Match existing Laravel and Vue style (PSR-12 for PHP; existing SFC patterns for Vue).
4. Do not commit `.env`, `vendor/`, `node_modules/`, logs, or compiled assets.
5. Open a pull request with a short summary of *why* the change exists and how to verify it.

Bug reports should include the page or route, expected vs actual behavior, and PHP/Node versions.

---

## License

This project is licensed under the [MIT License](https://opensource.org/licenses/MIT), as declared in `composer.json`.
