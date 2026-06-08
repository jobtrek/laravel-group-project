# CONTEXT.md — Project Map

> Internal **project-tracking app** for a foundation.
> Projects flow: **Proposition → Direction → Récolte → En cours**, with refused items archived in **"le frigo"**.
> Full requirements: [docs/summary.md](docs/summary.md) · Data model: [docs/mcd.mermaid](docs/mcd.mermaid)

# SUBJECT TO CHANGE SOON.
---

## Stack

| Layer       | Technology                                          |
|-------------|-----------------------------------------------------|
| Runtime     | PHP 8.3                                             |
| Framework   | Laravel 13                                          |
| Templates   | Blade (no JS framework)                             |
| ORM         | Eloquent                                            |
| Frontend    | Vite + Tailwind CSS v4                              |
| Database    | PostgreSQL 18 (via Sail) — `pgsql` service in `compose.yaml` |
| Queue/Mail  | Laravel queues + Laravel Mail                       |
| Dev tooling | Laravel Sail, Pail, Pao, Pint (PSR-12), PHPStan L5 |

---

## Commands

```bash
composer setup      # first-time: deps, .env, key, migrate, npm build
composer dev        # server + queue + pail + vite (concurrently)
composer test       # clears config cache, then runs PHPUnit
php artisan pint    # format PHP (PSR-12)
./vendor/bin/phpstan analyse  # static analysis (level 5, app/ only)
```

---

## Directory Map

```
laravel-group-project/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── Controller.php          # base controller (empty shell)
│   ├── Models/
│   │   └── User.php                    # auth user — role field drives permissions
│   └── Providers/
│       └── AppServiceProvider.php
│
├── bootstrap/
│   └── app.php                         # kernel / middleware / exception handler
│
├── config/                             # standard Laravel config files
│   ├── app.php
│   ├── auth.php
│   ├── cache.php, database.php, filesystems.php
│   ├── logging.php, mail.php
│   ├── queue.php, services.php, session.php
│
├── database/
│   ├── migrations/
│   │   ├── 000_create_users_table.php  # users + password_reset_tokens + sessions
│   │   ├── 000_create_cache_table.php
│   │   └── 000_create_jobs_table.php   # Laravel queue jobs table
│   ├── factories/
│   │   └── UserFactory.php
│   ├── seeders/
│   │   └── DatabaseSeeder.php
│   └── database.sqlite                 # unused leftover — actual DB is PostgreSQL via Sail
│
├── docs/
│   ├── summary.md                      # confirmed requirements (source of truth)
│   ├── mcd.mermaid                     # entity-relationship diagram
│   ├── correlation.md                  # open questions / conflicts between interviews
│   ├── insertitudes.md                 # uncertainties
│   ├── before-contacting.md / after-contacting.md / question.md
│   ├── nikita.md / thomas.md / tiziano.md  # raw interview notes
│   └── Canevas de proposition de projet.pdf
│
├── public/
│   └── index.php                       # front controller
│
├── resources/
│   ├── css/app.css                     # Tailwind entrypoint
│   ├── js/app.js                       # Vite JS entrypoint
│   └── views/
│       └── welcome.blade.php           # placeholder home view
│
├── routes/
│   ├── web.php                         # HTTP routes (only GET / → welcome for now)
│   └── console.php                     # Artisan schedule / closure commands
│
├── tests/
│   ├── TestCase.php
│   ├── Feature/ExampleTest.php
│   └── Unit/ExampleTest.php
│
├── AGENTS.md                           # agent / AI contributor context (symlink)
├── CLAUDE.md                           # Claude Code instructions
├── CONTEXT.md                          # this file
├── README.md
├── composer.json
├── package.json
├── phpstan.neon                        # PHPStan config (level 5, app/)
├── phpunit.xml                         # test config (SQLite in-memory for tests)
└── vite.config.js
```

---

## Domain Model (from [docs/mcd.mermaid](docs/mcd.mermaid))

```
USER ──proposes──► PROJECT ──has──► FUNDING
 │                    │
 │                    ├──contains──► COMMENT ◄──writes── USER
 │                    │
 │                    └──assigned──► PROJECT_TEAM ◄──contains── TEAM
 └──leads──► PROJECT
```

### Entities

| Entity       | Key fields                                                      |
|--------------|-----------------------------------------------------------------|
| `USER`       | id, name, email, **role**                                       |
| `PROJECT`    | id, title, **form_data** (JSON), **status**, created_at         |
| `COMMENT`    | id, project_id, author_id, content, created_at                  |
| `FUNDING`    | id, project_id, financier_id, **target_amount**, **found_amount**, updated_at |
| `TEAM`       | id, name                                                        |
| `PROJECT_TEAM` | project_id, team_id (pivot)                                   |

> **Note:** `form_data` is a JSON blob carrying the full proposition form (goal, description, budget, planning steps with taux, impact analysis).

---

## Roles & Permissions

| Role              | `users.role` value | What they can see / do                                                   |
|-------------------|--------------------|---------------------------------------------------------------------------|
| Proposer          | `proposer`         | Create proposals; see own projects; see all `en_cours` projects           |
| Project Lead      | `project_lead`     | See & comment on all `en_cours` projects (regardless of proposer)        |
| Direction         | `direction`        | See & comment on all `direction` projects; move projects between stages   |
| Récolte Manager   | `recolte_manager`  | See & comment on all `recolte` projects; move projects between stages     |

---

## Project Lifecycle

```
proposition  ──(Direction: yes)──►  direction  ──(accepted)──►  recolte  ──(≥80% funded)──►  en_cours
     │                                   │                          │                              │
     │                           (Direction: no)              (12 months)                    (3 months)
     │                                   │                          │                              │
     └───────────────────────────────────┴──────────────────────────┴──────────────────────────────┴──► archived
                                 refused → "frigo"              auto-archive                   auto-archive
```

### Status values on `projects.status`

| Value         | Meaning                                              |
|---------------|------------------------------------------------------|
| `proposition` | Newly submitted, awaiting Direction review           |
| `direction`   | Under review by Direction                            |
| `recolte`     | Accepted; gathering ≥ 80% of funding                 |
| `en_cours`    | Fully funded and active                              |
| `archived`    | Completed or auto-archived (per-stage archives)      |
| `frigo`       | Refused by Direction ("le frigo")                    |

---

## Business Rules

### 80% funding gate
A project moves from `recolte` → `en_cours` only when:
```
FUNDING.found_amount / FUNDING.target_amount >= 0.80
```

### Age-based colour coding (per stage)
| Age in current stage | Display     |
|----------------------|-------------|
| > 1 month            | Orange      |
| > 2 months           | Red         |
| > 3 months           | Auto-archived (except Récolte) |
| > 12 months (Récolte only) | Auto-archived |

### Email escalation for `en_cours`
1. No comment update for **1 month** → reminder email to project lead.
2. Still no update **1 week later** → escalation email CC'd to everyone with a follow-up role.

Implement via **queued jobs** (Laravel queue worker is started by `composer dev`).

### Archives
- Each stage has its own separate archive view.
- Archived projects can be restored to the stage they came from.

---

## What Still Needs to Be Built

The project is a **fresh Laravel skeleton** — only the default scaffold exists. Everything below is yet to be implemented:

- [ ] `role` column on `users` migration
- [ ] `projects` migration + `Project` model + relationships
- [ ] `funding` migration + `Funding` model
- [ ] `comments` migration + `Comment` model
- [ ] `teams` + `project_team` migrations + `Team` model
- [ ] Auth scaffolding (login / register / logout)
- [ ] Middleware / policy layer for role-scoped access
- [ ] Proposition form (goal, description, budget, planning, impact)
- [ ] Direction dashboard (filter/sort by impact, yes/no/more-info actions)
- [ ] Récolte dashboard (funding progress, green threshold indicator)
- [ ] En cours dashboard (team, resources, timestamped comment log)
- [ ] Le frigo view (refused projects)
- [ ] Per-stage archive views + restore action
- [ ] Age-based colour coding (computed attribute or query scope)
- [ ] Auto-archive scheduled command (`artisan schedule:run`)
- [ ] Email reminder + escalation queued jobs
- [ ] Sober Tailwind layout / design system
