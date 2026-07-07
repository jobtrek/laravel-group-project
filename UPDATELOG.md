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
