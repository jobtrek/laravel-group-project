# Issue #272: Proposition wizard validation (steps 1-3) only enforced client-side

## What changed
- `app/Http/Requests/PropositionRequest.php`: changed `perimetre` from `nullable` to `required`, matching the wizard's `validateStep1` which treats it as mandatory.
- `tests/Feature/PropositionValidationTest.php`: new feature test posting directly to `proposition.store`, bypassing the Alpine wizard, covering: a fully valid submission, a missing `perimetre`, an out-of-range `portee` (>50), an out-of-range `confiance` (>100), and a submission with no fields at all.

## Why
Auditing `PropositionRequest` against `validateStep1/2/3` in `wizard.blade.php` showed every field and numeric range already had a server-side mirror (`titre`, `description`, `buts`, phase fields, and the scoring fields `portee`/`impact`/`confiance`/`effort` via `HasScoringRules`, with ranges matching the JS: portee 0-50, confiance 0-100) — except `perimetre`, which the JS requires but the backend allowed as `nullable`. That was the one real gap, so the fix is a single rule change rather than a rewrite of the validation logic. The new tests lock in that the backend is the source of truth by simulating a client that skips the wizard's JS entirely.
