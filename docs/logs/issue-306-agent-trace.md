# Issue #306: Weekly reminder job resets last_reminder_at, escalation email never fires

## The issue

`mail:send-reminders` re-stamped `last_reminder_at = now()` on every weekly run while the
leader stayed silent. `mail:send-warnings` only escalates once that timestamp is a week old,
so it never aged past a few days and `StrongerEmailReminder` never fired.

Root cause: one column carried two meanings — "when we last pinged them" and "how long since
the first ping". The existing regression test was a false green (both command runs landed in
the same wall-clock second, so `equalTo()` held even against the buggy code).

## Changes

- `database/migrations/2026_08_12_090000_add_escalated_at_to_projects.php` — new nullable
  `escalated_at` → a single timestamp cannot tell whether `T` was written by the reminder or
  by the warning, so the warning had no way to know it had already fired.
- `app/Models/Project.php` — `escalated_at` datetime cast → written via `forceFill`, so it
  needs the cast but not `$fillable` (matches `last_reminder_at`).
- `app/Console/Commands/SendProgressReminders.php` — new `isDueForReminder()` guard: skip
  projects already reminded unless the leader commented since → one reminder per silence
  period, so the escalation clock actually ages. Also clears `escalated_at` on send → re-arms
  the next cycle, otherwise the second silence period could never escalate.
- `app/Console/Commands/SendProgressWarnings.php` — gate on `whereNull('escalated_at')` →
  escalation fires once, not weekly forever. Replaced
  `whereColumn('updated_at', '<', 'last_reminder_at')` with a `last_leader_comment_at` check
  → comments live in their own table and never touch `projects.updated_at`, so the old clause
  escalated leaders who had replied and suppressed ones whose project was merely edited.
  Switched to the shared `needingProgressReminder()` scope → both commands now use one
  definition of "the leader has gone quiet".
- `routes/console.php` — `mail:send-warnings` weekly → `dailyAt('09:00')` → weekly-on-Wednesday
  put Email #2 nine days after a Monday reminder, not the one week the AC requires. Safe to
  run daily only because `escalated_at` gates it.
- `tests/Feature/ProjectTransitions/ReminderEscalationTest.php` — rebuilt on `travel()` and
  `assertDispatchedTimes` → the old `assertDispatched` counted nothing. Three cases: the
  reminder→escalation timeline, cycle restart after a leader comment, and a comment
  suppressing the escalation.

## How it fits together

```
scheduler → mail:send-reminders (weekly)
              needingProgressReminder() scope  ── last_leader_comment_at subquery
              isDueForReminder()               ── silent 1 month AND not already reminded
              → SendMailProcess                ── writes last_reminder_at, clears escalated_at

scheduler → mail:send-warnings (daily)
              same scope + whereNull(escalated_at) + last_reminder_at older than 1 week
              + no leader comment since the reminder
              → SendStrongerMailProcess        ── writes escalated_at, leaves last_reminder_at
```

`last_reminder_at` now means "the reminder that opened the current silence period" and is
written exactly once per period; `escalated_at` closes that period. A leader comment is the
only thing that starts a new one.

## Verification

- `ReminderEscalationTest`: 4 passed, 21 assertions.
- Reverting either command alone fails 2 of the 4 — the tests bite on both sides of the fix.
- Full suite 69/73. The 4 failures (registration ×2, proposition redirect, contribution
  validation) reproduce identically on `main` — pre-existing, unrelated.
- `pint` clean, `phpstan analyse` 0 errors.

## Review notes

Haiku diff review: clean, no findings across the 5 changed files. Checked the `gte`/`lt`
boundaries in `isDueForReminder()` and the warning filter, that `addSelect()` in
`needingProgressReminder()` still emits `projects.*` (so `whereNull('escalated_at')` resolves),
double-send/never-send paths, migration reversibility, and that the cumulative `travel()` calls
match the asserted dispatch counts. Nothing rejected or left unresolved.

## Out of scope

`docs/Source_of_truth.md` says "any comment resets the inactivity clock", but
`scopeNeedingProgressReminder()` (`app/Models/Project.php:224-228`) counts only comments by
the leader, and `AutoArchiveProjects.php:110` shares that assumption. Left leader-only —
widening it would change auto-archive behaviour too. Worth its own issue.
