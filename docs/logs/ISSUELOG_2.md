# Issue #270: State-machine-owned columns are mass-assignable on Project model

## What changed
- `app/Models/Project.php`: removed `status`, `current_stage`, `archived_at`, `restored_at`, `last_reminder_at` from `$fillable`.
- `app/Actions/CreateProjectProposal.php`: proposal creation now builds the `Project` via `new Project([...safe fields])` + explicit `$project->status = ...` / `$project->current_stage = ...` assignment before `save()`, instead of passing state-machine fields into `Project::create()`.

## Why
`$fillable` previously included several columns that are only supposed to be written by the state machine (`ProjectService`) or internal setters. No current controller mass-assigned these, but any future `$project->update($request->all())` would have let a user set their own project's status/archive date directly, bypassing authorization.

`ProjectService` and `AutoArchiveProjects` already wrote these fields via direct attribute assignment (`$project->status->transitionTo(...)`, `$project->current_stage = ...`), so they were unaffected. `CreateProjectProposal` was the one place that relied on mass assignment for `status`/`current_stage` during initial proposal creation — updated to set those fields directly (same underlying `HasStates` cast behavior, just not routed through `$fillable`), preserving the existing behavior of proposals starting in `PropositionState`.

## Validation
- `./vendor/bin/pint --dirty` — passed
- `./vendor/bin/phpstan analyse` — passed, 0 errors
- `php artisan test --filter "ProjectTransitions|PropositionValidationTest|ResourceContributionTest"` — 28/30 passing; the 2 remaining failures are a pre-existing, unrelated Vite manifest issue (a `resources/views/Images/logo-white.svg` asset referenced via `Vite::asset()` is not part of the build entrypoints/manifest) reproducible on `main` and unaffected by this change.
