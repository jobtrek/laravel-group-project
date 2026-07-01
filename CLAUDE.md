# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this app is

Internal **project-tracking app** for a foundation (~35 collaborators). Projects flow through five stages: **Propositions → Review → Récolte → En cours → Archive**. All accounts are admin-created; no self-registration.

See `docs/Source_of_truth.md` for the authoritative requirements.

## Stack

- **PHP 8.3 / Laravel 13** — Blade templates, Eloquent ORM, spatie/laravel-model-states, queued mail
- **Frontend** — Vite + Tailwind CSS v4 (no JS framework, no inline styles)
- **Database** — PostgreSQL (Sail/Docker); SQLite (`DB_DATABASE=testing`) for tests

## Commands

```bash
composer setup             # first-time: deps, .env, key, migrate, npm build
composer dev               # server + queue + pail + vite concurrently (no Sail)
composer test              # clears config cache, then runs PHPUnit
./vendor/bin/sail up -d    # start Docker environment
./vendor/bin/sail pint     # format all PHP files (PSR-12)
./vendor/bin/sail pint --dirty  # format only modified files (same as pre-commit hook)
./vendor/bin/phpstan analyse    # static analysis, level 5, app/ only
sail artisan test --filter TestClassName  # run a single test class
```

The pre-commit hook (CaptainHook) runs `pint --dirty` and re-stages formatted files automatically. Install it once with `./vendor/bin/captainhook install`.

Mail testing uses Mailtrap — configure `MAIL_USERNAME` / `MAIL_PASSWORD` in `.env`. Both `queue:listen` and `schedule:work` must be running for scheduled mail to send.

## Architecture

### State machine

Project lifecycle is managed with `spatie/laravel-model-states`. All states live in `app/Models/States/`, extending `ProjectState`. Transitions are registered in `ProjectState::config()`.

The states are currently being updated reference it to the 'docs/Source_of_truth.md'


### Key business rules

- **80% threshold**: `SUM(amount_found) / SUM(amount_needed) >= 0.8` across all `PhaseResource` rows triggers `CollectingState → ReadyState`
- **Age-based colour coding**: orange at +1 month, red at +2 months, auto-archived at +3 months (Récolte exception: auto-archived at 12 months)
- **En cours email escalation**: `EmailReminder` at 1 month no comment → `StrongerEmailReminder` (CC all follow-up role) 1 week later — both dispatched as queued jobs; `last_reminder_at` tracks state
- Direction members cannot approve their own proposals
- `porteur` (proposer) cannot add/update resources in Récolte — that is the `recolte_manager`'s role

### Layers

- `app/Http/Controllers/` — thin controllers; delegate to service or model
- `app/Service/ProjectService.php` — state transition orchestration
- `app/Actions/` — single-purpose action classes
- `app/Models/` — Eloquent models; `Project::createProposal()` is the main factory method
- `app/Mail/` + `app/Jobs/` — queued mail for reminders/approvals/denials
- `resources/views/` — Blade templates with Tailwind utility classes

### Roles

Stored as `role` on `users`. Multiple roles per user are allowed.

| Role | Key access |
|---|---|
| `collaborateur` | Propose; see own projects + all En cours |
| `direction` | Approve / refuse / request revision; see all |
| `recolte_manager` | Update resources in Récolte; see all |
| `chef_de_projet` | Comment on En cours; mark complete; see all |
| `admin` | Full access; only role that assigns roles |


### client-side details
if you enquire regarding client-side details for permissions or roles. reference to the docs/Source_of_truth.dm

## Testing

Feature tests use a real SQLite database — do not mock Eloquent. Use `RefreshDatabase` on every feature test class. Prefer feature tests over unit tests for anything touching the DB or HTTP layer.
