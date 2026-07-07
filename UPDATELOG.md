# Update Log

## Fix #277: Unescaped `fallbackUrl` in comeBackButton onclick handler

`resources/views/components/projects-details/comeBackButton.blade.php` built its
`onclick` handler with `window.location.href = '{{ $fallbackUrl }}';`. Blade's
`{{ }}` only HTML-escapes, but this value sits inside a single-quoted JavaScript
string within an inline event handler attribute. A `fallbackUrl` containing a
single quote could break out of the string and inject arbitrary JS. All current
callers pass `route()`-generated URLs, so this was a latent rather than actively
exploited vector, but it was fixed anyway.

Replaced `'{{ $fallbackUrl }}'` with `@js($fallbackUrl)`, which encodes the value
via `Illuminate\Support\Js::from()` (JSON-encoding with `JSON_HEX_*` flags) and
supplies its own surrounding quotes — safe for both the JS string context and the
enclosing double-quoted HTML attribute. Verified by hand-simulating the encoding
logic with a payload containing `'`, `"`, `<`, `>`, and `&`: no raw special
characters survive in the rendered output.

Checked all three call sites (`edit.blade.php`, `projectsDetails.blade.php`,
`phase_details.blade.php`) — only `edit.blade.php` passes a `fallback-url`
(via `route()`); the others use the component's default (no fallback branch),
so behavior is unchanged for them. No other Blade templates were found using
`{{ }}` inside `onclick=`/`onchange=`/etc. attribute strings, so no other files
needed changes.
# Issue #265 — Self-approval guard in ProjectController@approve

**Finding:** The guard was already restored. `ProjectController::approve()` currently contains:

```php
abort_if($project->proposer_id === auth()->id() && ! auth()->user()->can('manage everything'), 403);
```

This was reinstated by commit `f9f319a` ("fix: allow admin to have every access, as asked by stakeholder"),
which intentionally added an exception for users with the `manage everything` permission (admins) on top
of the original proposer check. `deny()` still has the plain, non-admin-exempted check
(`$project->proposer_id === auth()->id() || ! $project->status instanceof EvaluationState`), which is expected
since admins aren't meant to be exempted from denial in the same way. No production code changed for this issue.

**What changed:** Added `tests/Feature/ProjectTransitions/ApproveSelfApprovalGuardTest.php`, a regression test
covering the actual gap: (1) a direction user cannot approve their own project (403, state unchanged), (2) a
direction user can approve a project proposed by someone else, and (3) an admin with `manage everything` can
approve their own project. All three pass against SQLite.

**Out of scope:** Consolidating this ad-hoc `abort_if` logic into a `ProjectPolicy` is tracked separately as
issue #266 — not addressed here.
# Update Log

## Remove dead `ProjectController@review` method (#267)

`ProjectController::review(Project $project)` (previously around line 39) called `ProjectService::review($project)` and redirected back with a `project-in-review` status. It had no route pointing to it anywhere in `routes/web.php`, no view referenced it, and no test exercised it. The identical behaviour is already reachable through `ProjectController::sendToDirection`, which routes are actually wired to and which calls the same `ProjectService::review($project)`. This was leftover dead code from a rename of the action to `sendToDirection`.

Removed the `review()` method entirely from `app/Http/Controllers/ProjectController.php`. `sendToDirection` was left untouched, so no user-facing behaviour changed. Confirmed via grep across `routes/`, `resources/`, and `tests/` that nothing references `review()`. Ran the Feature test suite for Project-related tests (`php artisan test --filter=Project`, with `DB_CONNECTION=sqlite` since this environment lacks `pdo_pgsql`); the same 6 pre-existing failures occur both before and after this change (unrelated `Spatie\Permission` seeding issue — "no permission named `manage everything`"), confirming this removal introduced no regressions.
