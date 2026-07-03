# Issues solved — permissions pass

Work done against `docs/specs/finish_apply_permissions.md`, cross-checked with the board at
https://github.com/orgs/jobtrek/projects/99/views/2. Each entry below names the GitHub issue,
what was wrong, how it was fixed, and exactly where to look to review it.

---

## #222 — Seeded roles do not match the roles defined in the spec

**Problem:** `RoleAndPermissionSeeder` created `user`, `project_leader`, `resources_manager` —
none of which match `docs/Source_of_truth.md` / `CLAUDE.md` (`collaborateur`, `direction`,
`recolte_manager`, `chef_de_projet`, `admin`). Route middleware and Blade checks elsewhere in the
app already referenced the *spec* names, which didn't exist in the DB — silently dead.

**Fix:** Renamed every role to the spec's vocabulary and re-pointed all permission grants and role
references at the new names.

**Where:**
- `database/seeders/RoleAndPermissionSeeder.php:40-70` — roles are now `collaborateur`, `admin`,
  `direction`, `chef_de_projet`, `project_manager`, `recolte_manager`.
- `database/seeders/UserSeeder.php:15-49` — demo users updated to the new role names; added a
  `chef_de_projet` demo user (`chef@mail.fr`) since none existed before.
- `app/Http/Requests/CommentRequest.php:29` — `hasRole('chef_de_projet')`.
- `resources/views/projectsDetails.blade.php:141` — `hasRole('chef_de_projet')`.

`project_manager` is kept as its own role (not one of Source_of_truth's five) because issues
#197/#208 (below) treat it as a real, separate role already in active use.

---

## #197 / #208 — Project manager role & "send to direction" ownership

**Problem:** `send to direction` permission was on the `project_manager` role in the seeder, but
during an earlier grilling pass (before I'd read the board) I reassigned it to the proposer
(`collaborateur`) based on the button's UI position. #197 (closed) and #208 (open, in progress)
both establish that **project_manager**, not the proposer, is the gatekeeper who pushes a
proposal to Direction.

**Fix:** Reverted — permission back on `project_manager`, no proposer-ownership check on the
action.

**Where:**
- `database/seeders/RoleAndPermissionSeeder.php:67-70` — `$projectManager->givePermissionTo(['archive projects', 'send to direction'])`.
- `app/Http/Controllers/ProjectController.php:61-66` (`sendToDirection`) — no ownership guard.
- `resources/views/components/projects/displayProjects.blade.php:44-51` — button gated only by
  `@can('send to direction')`, no proposer check.

**Not done:** #208 also asks for a confirmation modal ("prompt manager before submitting") with
its own double-validation middleware — that's a UI feature, still open, not built in this pass.

---

## #226 — CommentController and ProjectController::archive() are unrouted dead code

**Problem:** Both existed but had no route. `sendToDirection()` didn't even exist as a method
(the route pointed at a name that would 500). The comment route path was also malformed:
`projects/{project}/projects/{project}/comments` (double segment, from nesting a full path
inside an already-prefixed route group).

**Fix:**
- Added `ProjectController::sendToDirection()` — `app/Http/Controllers/ProjectController.php:61-66`.
- Fixed the comment route to `/comments` (relative to the existing `/projects/{project}` prefix) —
  `routes/web.php:70-71`.
- Archive is now routed with `can:archive projects` — `routes/web.php:69`.
- Wired an actual comment-submission form (there was a display-only comments list before, no way
  to post one) — `resources/views/projectsDetails.blade.php:140-152`, gated by role + project
  state (see #223 below).

---

## #144 — Any authenticated user can resubmit any project

**Fix:** Added proposer-ownership guard.
**Where:** `app/Http/Controllers/ProjectController.php:85` (`reSubmit`) —
`abort_if($project->proposer_id !== auth()->id(), 403);`

---

## #145 — Direction members can approve their own proposals

**Fix:** Added a guard blocking a proposer from approving/denying their own project.
**Where:**
- `app/Http/Controllers/ProjectController.php:45` (`approve`)
- `app/Http/Controllers/ProjectController.php:54` (`deny`)

---

## #218 — complete / move-to-en-cours / team / resource-store routes have no authorization

**Fix:** Added role-permission middleware to every route, plus per-project ownership checks in
the controllers (role alone isn't enough — e.g. a `chef_de_projet` should only complete/launch
*their own* project).

**Where:**
- `routes/web.php:68` — `projects.complete` → `can:complete project`
- `routes/web.php:82-84` — `projects.recolte.activate` → `can:launch project`
- `routes/web.php:86-88` — `projects.recolte.team` → `can:assign team`
- `routes/web.php:75-80` — `projects.resources.create`/`store` already had `can:add resources`
  (unchanged, was already correct)
- `app/Http/Controllers/ProjectController.php:121` (`complete`) —
  `abort_if($project->leader_id !== auth()->id(), 403);`
- `app/Http/Controllers/RecolteController.php:38` (`moveFromRecolteToActive`) — same
  leader-ownership guard.
- New permissions backing the middleware: `archive projects`, `assign team`, `launch project`,
  `complete project` — `database/seeders/RoleAndPermissionSeeder.php:28-31`, granted to
  `chef_de_projet` (launch/complete) and `recolte_manager` (assign team) at lines 57-65.

Two more route-level bugs found and fixed in the same pass (not separately tracked on the board):
- `routes/web.php:31-34` — the revision-form/revision-submit routes required `can:review`
  (a Direction-only permission) even though they're proposer-only actions; this permanently
  blocked the actual proposer. Middleware removed; ownership is enforced in
  `app/Http/Requests/RevisionRequest.php:13-17` and already existed in
  `app/Http/Controllers/RevisionController.php:16-18,43-45`.
- `routes/web.php:70-71` — comment route path bug, see #226 above.

---

## #223 — No view-level role gating

**Problem:** No Blade view used `@can`/`hasRole`. Buttons rendered for every user regardless of
permission; some (e.g. `complete`) had no server-side check either, so clicking them actually
worked for anyone.

**Fix:** Added `@can`/role+ownership gating around every state-changing button, and added the two
UI elements whose backend existed but had no way to trigger them.

**Where (all in `resources/views/`):**
- `components/projects/displayProjects.blade.php:44-51` — send-to-direction: `@can('send to direction')`
- `components/projects/displayProjects.blade.php:54-70` — deny/révision/approve: `@can('evaluate projects')` (unchanged, pre-existing)
- `components/projects/displayProjects.blade.php:71-73` — complete: `@can('complete project')` + leader check
- `components/projects/displayProjects.blade.php:91-101` — add-resources (unchanged) + new
  "Démarrer le projet" (launch) button: `@can('launch project')` + leader check
- `resource-contribution-form.blade.php:118-129` — launch button (duplicate surface, only visible
  to `recolte_manager` via the page's own `can:add resources` gate) now also requires
  `can('launch project')` + leader check
- `projectsDetails.blade.php:140-152` — new comment form, gated:
  `hasRole('direction') && EvaluationState` OR `hasRole('chef_de_projet') && EncoursState`

---

## #148 — All FormRequest authorize() methods return true with no actual authorization logic

**Fix:** Replaced the stub `true` in each with real logic (or, for one, an explicit comment
explaining why `true` is actually correct rather than an oversight).

**Where:**
- `app/Http/Requests/PropositionRequest.php:13-16` — `hasRole('collaborateur')`
- `app/Http/Requests/RevisionRequest.php:11-15` — route-bound project's `proposer_id` must match
  the current user
- `app/Http/Requests/RequestMoreInfoRequest.php:10-13` — `hasRole('direction')`
- `app/Http/Requests/FilterProjectsRequest.php:11-16` — left as "any authenticated user," with a
  comment: Source_of_truth.md states all roles can see all projects, so this was never a real gap

`AssignProjectTeamRequest` and `StoreResourceContributionRequest` also return `true` unconditionally
but were **not** in scope for #148 (not named in the issue) — they're covered instead by the
route-level `can:assign team` / `can:add resources` middleware added under #218.

---

## Checked, no action needed

- **#146** (`/direction/projects` open to all authenticated users) and **#157**
  (`testDirectionFront.blade.php` reachable in production) — neither the route nor the view exists
  anywhere in the current codebase. Nothing to fix; likely already removed in earlier work not
  reflected in the board's status.

## Explicitly out of scope (left for follow-up)

- **#200** — admin user-creation/role-assignment UI. No such UI exists anywhere; agreed with
  Thomas this is a separate task.
- **#208** — the send-to-direction confirmation modal itself (only the underlying permission
  ownership was fixed).

---

## How to review

1. `./vendor/bin/sail up -d`, then `./vendor/bin/sail artisan migrate:fresh --seed` to get the
   renamed roles + demo users (`chef@mail.fr` / `pmanager@mail.fr` / `support@mail.fr` /
   `direction@mail.fr` / `admin@mail.fr`, all on the factory default password).
2. Log in as each demo user and walk a project through Proposition → Évaluation → Récolte →
  En cours, confirming each stage's buttons only appear for the intended role and 403 for others.
3. `git diff` covers: `database/seeders/RoleAndPermissionSeeder.php`,
   `database/seeders/UserSeeder.php`, `routes/web.php`, `app/Http/Controllers/ProjectController.php`,
   `app/Http/Controllers/RecolteController.php`, `app/Http/Requests/*.php`, and the three Blade
   files listed under #223.

---

## Manual walkthrough performed (via agent-browser, this session)

Ran the review checklist above end-to-end against a real seeded environment (`sail up`,
`migrate:fresh --seed`) and confirmed both the UI gating and the server-side 403s, not just the
code:

| Check | Result |
|---|---|
| `project_manager` sees "Confirmer la proposition"; clicking it moves a project Proposition → Évaluation | ✅ verified (project #73) |
| `collaborateur` (no other role) sees **no** send-to-direction button anywhere, and a direct POST to the route returns 403 | ✅ verified |
| `collaborateur` sees no Refuser/Révision/Accepter buttons on `/evaluation` | ✅ verified |
| `direction` sees those buttons; approving/denying their **own** proposal returns 403 even though authenticated as direction | ✅ verified (project #86) |
| `recolte_manager` sees "Ajouter une ressource" + team-assign form on a Récolte project, but never the launch ("Démarrer le projet") button, even after a leader is assigned | ✅ verified (project #56) |
| `chef_de_projet` who **is** the assigned leader sees and can use the launch button; clicking it actually transitions Récolte → En cours | ✅ verified (project #56) |
| `chef_de_projet` (as leader) sees the comment form on their En cours project and a real comment posts with `stage = "en cours"` | ✅ verified |
| `collaborateur` sees no comment form on that same project, and a direct POST to `/comments` returns 403 | ✅ verified |
| `collaborateur` gets 403 on a direct POST to `/archive`; `direction` gets 200 and the project's status becomes `archivé` | ✅ verified (project #7) |

**One real gap found, not previously called out:** on `/evaluation`, the Refuser/Révision/Accepter
buttons still render for a `direction` user on **their own** proposal — the 403 guard added for
#145 is server-side only (`ProjectController.php:45,54`), there's no matching Blade-level
`@if(auth()->id() !== $project->proposer_id)` around those buttons in
`displayProjects.blade.php:54-70`. Not a security hole (server rejects it either way), but a
confusing UX: direction sees a button that will 403 if clicked. Worth a follow-up if you want it
hidden rather than just rejected.
