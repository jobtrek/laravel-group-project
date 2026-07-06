# Update — removing hardcoded values (issue #166)

## Config

- Added `config/projects.php`. Holds pagination size, reminder/escalation timing, staleness/archive thresholds, and PICE scoring bounds, all overridable via new `PROJECTS_*` env vars (added to `.env` and `.env.example`).

## Roles

- Added `App\Enums\Role` backed enum, mirroring the role names seeded by `RoleAndPermissionSeeder`. Prevents typos causing silent auth mismatches between code and DB.
- `RoleAndPermissionSeeder`, `RequestMoreInfoRequest`, `CommentRequest`, `PropositionRequest`, `AuthenticatedSessionController` now use `Role::X->value` instead of raw strings.

## Récolte archiving — removed duplicate command

- `app/Console/Commands/RecolteArchiving.php`, its schedule entry, `RecolteArchivingMail`, and its Blade view were deleted. It duplicated `AutoArchiveProjects::archiveStaleRecolte()` with a different (and looser) threshold, causing two daily jobs to race over the same projects.
- `AutoArchiveProjects` is now the single source of truth for Récolte archiving; its threshold reads from `config('projects.recolte_archive_after_months')`.
- `tests/Feature/ProjectTransitions/ArchiveTest.php` updated to test `AutoArchiveProjects` archiving Récolte projects instead of the deleted command.

## Validation

- Added `App\Http\Requests\Concerns\HasScoringRules` trait. `UpdateProjectRequest` and `PropositionRequest` had identical, duplicated `portee`/`impact`/`confiance`/`effort` bounds — now built once from `config('projects.scoring')`.

## Misc

- `ProjectController` and `StageProjectController` pagination (`paginate(10)`) now reads `config('projects.per_page')`.
- `displayProjects.blade.php` staleness colour thresholds (1/2/3 months) now read from `config('projects.staleness_colors')`.
- `routes/console.php` reminder (`subMonth`) and escalation (`subWeek`) timing now read from config. Cron schedule day/time literals were left as-is — that's scheduling config, not a business-rule constant.

## Out of scope

- The 80% "ready" threshold mentioned in issue #166 doesn't exist as a state-transition rule in this codebase — it's only a progress-bar colour cutoff in the UI, confirmed with Thomas as intentionally out of scope for this fix.
