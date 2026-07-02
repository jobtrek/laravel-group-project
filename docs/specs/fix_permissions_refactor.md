# Issue #202 — Resource Contribution Form Fixes & Improvements

> Status: **Implemented**. See "Resolutions" under each section for what was actually built and why.

## 1. Chef de Projet Validation (Récolte → En Cours)

- Currently, selecting the placeholder option (e.g. *"sélectionner un chef de projet"*) and clicking **démarrer** still saves the project to the DB and transitions it to **en cours**. This should not happen.
- A **chef de projet must be selected** before a project can be transitioned to **en cours**.
- If a user attempts to transition without selecting a chef de projet:
  - The request must **not save to the DB**.
  - An **error message must be shown** (UX-level feedback).
- Validation must be implemented on **both frontend and backend**.

### Resolution

Root cause: "Équipe du projet" (save chief/members) and "Démarrer le projet" (transition) were two independent requests. The activate action only re-checked the DB's `leader_id`, so it never actually looked at what was currently selected in the (unsaved) dropdown — a stale/pre-existing `leader_id` on the project record could let `démarrer` succeed even after the user reset the dropdown back to the placeholder.

Fix: merged "Enregistrer l'équipe" and "Démarrer le projet" into a single form/request (`RecolteController::moveFromRecolteToActive`, still `PATCH projects.recolte.activate`), validated by `AssignProjectTeamRequest` (`leader_id` required, `exists:users,id`). Validation runs before any write, so a missing/invalid chief never touches the DB and redirects back with an `assignTeam`-bagged error, rendered on the page. Frontend: the submit button (`form="team-form"`) is `:disabled="!chiefId"`, driven by live Alpine state — a second, immediate layer of feedback on top of the server-side guard. The dedicated `projects.recolte.team` route/`assignTeam` method were removed since team assignment no longer exists as a standalone action.

## 2. Resource Contribution Form — Layout & Defaults

- Remove all **default/pre-selected options** for:
  - Chef de projet
  - Members
- When an option is disabled, it's using something like
```HTML
 <option value="" selected disabled hidden>Choose here</option>
```
- Both dropdowns should start **empty** — the user must actively make a selection.
- Move the **"Démarrer le projet"** button so it appears **below both forms**:
  - Équipe du projet
  - Ajouter une ressource
- The button should be:
  - **Disabled** until a chef de projet is selected.
  - **Enabled** once a chef de projet is selected.

### Resolution

Grilled two open questions before implementing (see conversation for full reasoning):

- **Should an already-persisted `leader_id`/members re-appear pre-selected on reload?** Initially decided no, on the reasoning that it would reintroduce a "pre-selected option." Corrected after feedback: **yes** — when a project already has a chief (and members) saved, the dropdowns should reflect that instead of forcing a redundant re-pick every visit. What §2's "no default/pre-selected options" line is actually targeting is the *first-time* state (a brand-new/never-assigned project shouldn't arbitrarily default to some option); it isn't meant to hide already-real, previously-saved data. This is safe against the §1 bug because of the merge below — the chief shown is always exactly what gets (re-)submitted, not a value the backend trusts independently of the form.
- **Merge démarrer + team-assign, or keep them separate?** Decided **merge** (see §1) — this is what makes "disabled until chief selected" and "must not save without a chief" coherent as a single guarantee instead of two independently-checked code paths, and what makes pre-filling from the DB safe (see above).

Implementation: chief `<select>` uses the exact `<option value="" selected disabled hidden>` placeholder pattern requested, but the Alpine model defaults to `old('leader_id', (string) $project->leader_id)` — blank for a project with no chief yet, otherwise the existing chief. Members multi-select (`<x-user-select-list>`) likewise initializes from `old('membres', $project->members->pluck('id')->all() ?: [''])`. The démarrer button lives outside both `<form>` elements, after the resource form, and submits the team form via `form="team-form"`; its `:disabled`/`:class` are bound to a `chiefId` Alpine value scoped to the whole page container so it reacts to the live dropdown regardless of which form it's visually nested under.

## 3. Phase Details / Project Details Pages

- When resources are added, the update should be reflected in:
  - The **progress bar**, and
  - The **type of resource section** within `resources/views/phase_details.blade.php`.
- Confirm exact file paths for `resources/views/phase_details.blade.php` and `resources/views/projectsDetails.blade.php` (see clarification below).

### Resolution

Confirmed both files exist as named (`resources/views/phase_details.blade.php`, `resources/views/projectsDetails.blade.php`) — no path correction needed. `projectsDetails.blade.php` already showed a live progress bar via `$project->progress` (derived from all phases' contributions), so it needed no change.

`phase_details.blade.php` had neither a progress bar nor any reflection of contributed amounts — the "ressources" section only ever showed the static `amount_needed` from `PhaseResource`, never what had actually been contributed. Added:
- A progress bar using the existing `ProjectPhase::progress` accessor.
- Per-resource "found / needed" amounts, summing `ResourceContribution` rows by matching `resource_type` within the phase.

Also fixed a real bug found along the way: the `phase_details` route wasn't eager-loading `resources`/`contributions`, so this data was only ever available via N+1 lazy loads. Added `$phase->load(['resources', 'contributions'])` to the route closure in `routes/web.php`.

## 4. Technical — Reduce Duplication in `app.js`

- `app.js` currently duplicates logic already present in `wizard.blade.php` for adding elements (e.g. the "+" button pattern).
- Centralize this logic in one reusable place rather than duplicating/hardcoding it per form.

### Resolution

Extracted the "push empty value" / "splice with a minimum length guard" pattern into `window.listHelpers` (`resources/js/app.js`), used by:
- `Alpine.data('userMultiSelect', ...)` (backs `<x-user-select-list>`, used for the récolte members picker).
- `wizard.blade.php`'s `removeItem(arr, i)` helper (already shared across buts/objectifs/livrables).
- `step-1.blade.php`'s membres add/remove buttons, which previously called `membres.push('')` / `membres.splice(idx, 1)` inline instead of going through the wizard's own `removeItem` helper.

## 5. Tools

- You may use the **Laravel systematic debugging** skill if you run into issues.
- You may use the **Agents browser skill** to test the feature yourself
- You may use the **Laravel best practices** skill while implementing this feature.

### Verification performed

- New feature test `tests/Feature/ProjectTransitions/MoveFromRecolteToActiveTest.php`: asserts an empty `leader_id` is rejected with a validation error and no DB write, and a valid submission persists chief/members and transitions to `EncoursState` in one request.
- Full test suite, `phpstan analyse`, and `pint --dirty` all pass (pre-existing unrelated failures only: missing pgsql driver / `UserFactory::unverified()`).
- Exercised the real flow end-to-end via `agent-browser` against a seeded local DB: confirmed the placeholder is disabled/hidden and both dropdowns start empty for a project with no chief yet (Démarrer disabled until one is picked); confirmed a project with a pre-existing chief/members shows them pre-selected (Démarrer enabled immediately); confirmed submitting transitions the project to `en cours` either way.
