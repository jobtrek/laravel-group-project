# CLAUDE.md

## Project overview

Internal **project-tracking** app (not a management tool) for a foundation.
Projects flow through four stages: **Proposition → Direction → Récolte → En cours**, with refused projects archived in **"le frigo"**.

See @docs/summary.md for the full requirements and @docs/mcd.mermaid for the data model.

## Stack

- **PHP 8.3 / Laravel 13** — Blade templates, Eloquent ORM, Laravel queues/mail
- **Frontend** — Vite + Tailwind CSS v4 (no JS framework)
- **Database** — PostgreSQL 18 via Laravel Sail (`compose.yaml`)

## Commands

```bash
composer setup      # first-time install: deps, .env, key, migrate, npm build
composer dev        # starts server + queue + pail + vite concurrently
composer test       # clears config cache, then runs PHPUnit
php artisan pint    # format PHP (PSR-12 via Laravel Pint)
./vendor/bin/phpstan analyse  # static analysis (level 5, app/ only)
```

After making changes, always run `composer test` and `php artisan pint` before committing.

## Code style

- PHP: PSR-12, enforced by Pint — run it, don't hand-fix formatting
- Blade: keep logic out of views; use view composers or `@inject` sparingly
- No inline styles — use Tailwind utility classes only
- Keep controllers thin; business logic lives in service classes or model methods

## Architecture

- **Roles** (stored as `role` on `users`): `proposer`, `project_lead`, `direction`, `recolte_manager`
- **Lifecycle statuses** on `projects.status`: `proposition`, `direction`, `recolte`, `en_cours`, `archived`, `frigo`
- Permissions are **role + status scoped** — a proposer only sees their own projects except in `en_cours` (visible to all)
- The **80 % funding rule** (`found_amount / target_amount >= 0.8`) gates the `recolte → en_cours` transition
- **Age-based colour coding** is per-stage: orange at +1 month, red at +2 months, auto-archived at +3 months (Récolte auto-archives at 12 months)
- **Email escalation** for `en_cours`: reminder at 1 month stale, escalation (CC all follow-up roles) 1 week later — implement via queued jobs

## Testing

- Feature tests hit a real SQLite database (`DB_DATABASE=testing`) — do not mock Eloquent
- Use `RefreshDatabase` on every feature test class
- Prefer feature tests over unit tests for anything touching the DB or HTTP layer
- Run a single test with `php artisan test --filter TestClassName`

## Git

- Branch naming: `<type>/<short-description>` (e.g. `feat/proposition-form`, `fix/recolte-threshold`)
- PRs target `main`; no force-pushing to `main`