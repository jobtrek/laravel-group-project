---
name: static-code-auditor
description: >-
  Performs a strict, two-phase static analysis of a codebase (frontend, then
  backend) to surface critical security vulnerabilities, bugs, dead code, and
  architectural anti-patterns. Use this whenever the user asks for a code
  review, audit, security review, vulnerability scan, bug hunt, or static
  analysis of any codebase or project, even if they don't use those exact
  words (e.g. "look over my code", "is this secure", "what's wrong with this
  project", "check my repo for issues"). Backend analysis is always graded
  strictly against official Laravel 13 documentation best practices; frontend
  analysis is framework-agnostic. Findings only, never code fixes, diffs,
  JSON schemas, or diagrams, only named issues with explanations of impact
  and severity.
---

# Two-Phase Static Code Auditor

A strict, standards-based auditor that scans a codebase in two clearly separated
passes — frontend, then backend — and reports only the most critical findings in
each. This skill is about **signal over noise**: a short list of severe, well-argued
issues beats a long list of nitpicks.

## When this triggers

Use this skill for requests like:
- "Review/audit my codebase for issues"
- "Is this app secure?" / "Find vulnerabilities in this project"
- "What's wrong with this code?" / "Static analysis of my repo"
- "Check for bugs / dead code / bad practices"

It applies regardless of whether the user names a specific framework. If the
backend is Laravel, Phase 2 below applies in full. If the backend uses a
different framework, still run Phase 2's category list, but ground the
"best practices" judgment in that framework's own official docs (fetch them)
instead of Laravel's.

## Before you start

1. **Locate the codebase.** If the user references files/paths/an upload/a repo, read it directly — don't ask them to paste code that's already accessible. If nothing is actually accessible (no upload, no path, no repo access), say so and ask for it; do not analyze from memory or invent a codebase.
2. **Identify the split.** Determine which files/directories are frontend (views, Blade templates, Alpine/Vue/React components, JS/CSS assets, public-facing routes' client code) versus backend (PHP application code: `app/`, `routes/`, `config/`, `database/`, `bootstrap/`, etc., for Laravel). If the boundary is ambiguous (e.g. Blade files that mix server-rendered PHP and Alpine directives), classify by where the logic executes: server-side templating logic is backend, client-side interactivity (Alpine/JS handlers, fetch calls, DOM manipulation) is frontend.
3. **Refresh the standard.** For the backend phase, do not rely purely on memorized framework knowledge — Laravel version-specific conventions change across major versions. Before judging backend code, fetch the relevant page(s) of the official Laravel 13 docs (https://laravel.com/docs/13.x/readme) for the categories that show up in the code (see `references/laravel-13-checklist.md` for a category → doc-page map). This keeps findings grounded in the actual current standard rather than a stale mental model.

## Execution: strict two-phase process

Run the phases **in order** and keep their outputs **completely separate** — do
not interleave frontend and backend findings, and do not let a phase's list
bleed into the other's section. Announce each phase with a clear heading before
its findings.

### Phase 1 — Frontend Analysis

Scan for, in priority order of what tends to matter most:
- **Security vulnerabilities**: XSS (unescaped output, `v-html`/`dangerouslySetInnerHTML`/`{!! !!}` misuse), exposed secrets/API keys in client code, insecure direct object references driven by client-editable state, CSRF token mishandling, unsafe `eval`/`innerHTML` usage, insecure third-party script inclusion.
- **Functional bugs**: broken state management, race conditions in async calls, incorrect conditional rendering, off-by-one or boundary errors.
- **Edge-case bugs**: unhandled empty/null/loading/error states, unvalidated user input before submission, assumptions that break on slow networks or rapid re-renders.
- **Dead code / unused components**: unreferenced components, unreachable branches, commented-out blocks left in place, unused imports/props/variables.
- **Anti-patterns**: prop drilling where context/state management would be idiomatic, direct DOM manipulation in a component-based framework, missing keys in lists, tightly coupled presentation/logic, inline styles replacing a design system without reason.

Select only the **5 most critical** issues found. You may extend to a **maximum
of 10** only if the codebase has an unusually high volume of severe (not minor)
issues — extending the list to hit a quota is not acceptable; every extra issue
must independently clear the same severity bar as the first 5.

### Phase 2 — Backend Analysis (Laravel 13)

Scan for:
- **Security vulnerabilities**: mass assignment via unguarded/unfiltered `fillable`/`guarded`, raw query injection (`DB::raw`, unparameterized `whereRaw`/`selectRaw`), broken or missing authorization (controller actions with no Policy/Gate check, over-permissive Gate definitions), missing validation on request input, insecure direct object references in route model binding, unsafe file upload handling, secrets committed to config/`.env.example`/version control, missing rate limiting on sensitive endpoints.
- **Dead code / unused elements**: unused routes, controllers/methods never referenced, unused migrations left inconsistent with the schema, unused service classes/jobs/listeners, orphaned Blade views.
- **Bugs / edge cases**: N+1 query patterns, missing database transactions around multi-step writes, race conditions in queued jobs, incorrect Eloquent relationship definitions, timezone/locale handling errors, unhandled failure paths in queued/async work.
- **Laravel 13 best-practice deviations**: bypassing Eloquent/query builder in favor of raw SQL without justification, business logic in controllers instead of actions/services, fat models or fat controllers, not using Form Requests for validation, ignoring Laravel's authorization system (Policies/Gates) in favor of ad-hoc checks, improper use of facades vs. dependency injection where testability matters, misuse of the container/service bindings.

Same filtering rule as Phase 1: 5 most critical by default, up to 10 only for
codebases with genuinely high volumes of severe flaws.

## Output format (strict)

For **every** issue in both phases, use this structure:

**Issue name/location** — e.g. `UserController@update — Mass Assignment` or
`Dashboard.jsx — Unescaped user bio in profile card`.

Then a explanation covering, in prose (not bullet fragments), all of:
- *What the issue is* — precise enough to locate it.
- *Why it's a problem* — the mechanism, not just a label.
- *Impact* — what actually happens if this ships (data breach, crash, wrong
  charge, silent data corruption, etc.). Be concrete, not generic ("this co3uld
  be bad") — say what an attacker or user actually experiences.
- *Best-practice basis* — for backend issues, name the Laravel 13 convention or
  doc-backed pattern being violated. For frontend, name the general principle
  or framework convention being violated.
- To *create these issues*, you need to use gh on this board `https://github.com/orgs/jobtrek/projects/99`, you may not use emojis, however the structure 
will be very simple, the title will be what the issue, then 1-2 phrases explaining why it's a problem, and in bullet points. you will write a simple acceptance
criteria, if there are any information that contradicts this information then you may start a grill session with me, and also, use the tag BUG and NEW


## Hard constraints (do not violate)

- **No code.** Never include code snippets, diffs, pseudo-code, JSON, or
  structural/architecture diagrams anywhere in the output — findings are
  described in prose only, even when referencing a specific line's behavior.
- **No mixing phases.** Frontend and backend findings must appear under their
  own clearly labeled headings, never interleaved.
- **Quality over quantity.** 5 sharp, high-severity findings per phase are
  the target; padding to reach 10 with minor style nitpicks is a failure mode.
- **Tone**: professional, analytical, direct. No hedging filler, no praise
  padding, no apologizing for critical findings.
- **Grounded, not invented**: only report what the code actually shows. If a
  category (e.g. authorization) has no issues, omit it rather than inventing a
  minor complaint to fill a slot.

## Reference

See `references/laravel-13-checklist.md` for the category → official-doc-page
map used to verify backend findings against current Laravel 13 conventions
rather than memorized/stale framework knowledge.
