# Update Log

## Remove dead `ProjectController@review` method (#267)

`ProjectController::review(Project $project)` (previously around line 39) called `ProjectService::review($project)` and redirected back with a `project-in-review` status. It had no route pointing to it anywhere in `routes/web.php`, no view referenced it, and no test exercised it. The identical behaviour is already reachable through `ProjectController::sendToDirection`, which routes are actually wired to and which calls the same `ProjectService::review($project)`. This was leftover dead code from a rename of the action to `sendToDirection`.

Removed the `review()` method entirely from `app/Http/Controllers/ProjectController.php`. `sendToDirection` was left untouched, so no user-facing behaviour changed. Confirmed via grep across `routes/`, `resources/`, and `tests/` that nothing references `review()`. Ran the Feature test suite for Project-related tests (`php artisan test --filter=Project`, with `DB_CONNECTION=sqlite` since this environment lacks `pdo_pgsql`); the same 6 pre-existing failures occur both before and after this change (unrelated `Spatie\Permission` seeding issue — "no permission named `manage everything`"), confirming this removal introduced no regressions.
