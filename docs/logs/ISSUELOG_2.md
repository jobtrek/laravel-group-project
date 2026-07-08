# Issue #321: allProjects.blade.php + displayProjects.blade.php — hard-coded status string comparisons

## What changed
- `app/Models/Project.php`: added six helper methods (`isArchived()`, `isCompleted()`, `isProposition()`, `isInEvaluation()`, `isInProgress()`, `isInRecolte()`) that check `$this->status instanceof <StateClass>` against `ArchiveState`, `CompleteState`, `PropositionState`, `EvaluationState`, `EncoursState`, `RecolteState`.
- `resources/views/allProjects.blade.php`: replaced `$project->status !== 'archivé' && $project->status !== 'complété'` with `!$project->isArchived() && !$project->isCompleted()`.
- `resources/views/components/projects/displayProjects.blade.php`: replaced all four `(string)$status === '...'` comparisons (proposition, évaluation, en cours, récolte — including the reused `en cours`/`récolte` checks for the progress bar and launch button) with calls to the new `$project->isProposition()`, `isInEvaluation()`, `isInProgress()`, `isInRecolte()` helpers.

## Why
The raw string comparisons duplicated each `ProjectState` subclass's `$name` value in Blade, with no compile-time link between them. Renaming a `$name` property would silently break buttons/filters/progress bars with no exception. Moving the check into `instanceof`-based helper methods on `Project` ties the comparison directly to the state class, so a rename or refactor of a state's `$name` no longer affects these checks at all, and Blade templates stay free of state-name string knowledge. Helper methods (approach b) were chosen over inline `instanceof` in Blade to keep templates declarative and reusable, matching the existing `canComment()` pattern already on `Project` (which does the same `instanceof EncoursState` check).

## Validation
- `./vendor/bin/pint` — clean (touched files auto-fixed; two unrelated pre-existing formatting issues pint found in `RecolteController.php`/`ResourceContributionController.php` were reverted to keep this change scoped to the issue).
- `./vendor/bin/phpstan analyse` — passed, 0 errors.
- `php artisan test --filter Project` — could not produce a meaningful signal in this worktree: the worktree's directory name (`laravel-group-project-issue-321`) contains hyphens, which breaks Pest's generated test namespace (`Home\thomas\Desktop\laravelgroupprojectissue321\...`), causing `Call to undefined method ...::get()/seed()` errors unrelated to this change. Faker/Mail/Bus fakes also fail to resolve in this worktree, pointing to a pre-existing environment issue rather than a regression from this fix. No failure was related to status comparisons or the changed files.
