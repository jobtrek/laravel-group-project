# Issue #376: Consolidate five hand-rolled abort_if authorization checks into ProjectPolicy

## The issue
The "owner or admin" (plus a few state/role variants) rule was hand-written five times across two
controllers and one Blade view with `abort_if()` / inline booleans, instead of living in `ProjectPolicy`.

## Changes
- `app/Policies/ProjectPolicy.php` — added `resubmit()`, `update()`, `complete()`, `submitRevision()` → single source of truth per rule, unit-testable in isolation.
- `app/Http/Controllers/ProjectController.php` — `reSubmit()`, `edit()`, `complete()` now call `Gate::authorize(...)` instead of inline `abort_if()` → removes duplicated logic, dropped now-unused `Role`/`EncoursState`/`RevisionState` imports.
- `app/Http/Controllers/RevisionController.php` — `showForm()` and `submit()` now call `Gate::authorize('submitRevision', $project)` instead of the duplicated inline check → same rule, one place.
- `resources/views/components/projects/displayProjects.blade.php` — replaced the retyped 4-clause `@if` for the "complete" button with `@can('complete', $project)` → view can no longer drift from the controller rule.

## How it fits together
- `Gate::before` in `AppServiceProvider` already bypasses every policy check for `manage everything` — so policy methods only need to encode the ownership/state/role part, not repeat the admin bypass (mirrors the existing `review`/`comment` methods).
- `complete()` additionally re-checks `$user->can('complete project')` inside the policy (not just via the `can:complete project` route middleware), because the Blade `@can` call goes through the policy directly and needs the same visibility rule the controller route enforces.
- Role-based-only middleware (`can:complete project`, `can:approve`, etc.) stays on the routes; the policy is only responsible for the ownership/instance-level part of each rule, consistent with the existing `review` policy comment.

## Review notes
- Haiku review pass (diff + behavioral-equivalence check against original `abort_if`/inline conditions): no issues found.
- `package-lock.json` got touched by `npm install` in the worktree (needed to build Vite assets for test runs) — reverted, out of scope.
