# Clarification — New Conflicts Found (2026-06-25)

*These conflicts were discovered by cross-referencing the updated `densified-summary.md` (with client answers) against the existing docs and the actual codebase.*

---

## Conflict A: Archive restoration — "all go back to Proposition" vs. stage-specific

- **densified-summary.md** (Archives section, point 5): *"If a Project comes back from archived no matter what stage they were, they go back into the proposition stage."*
- **Codebase** (`app/Models/States/ProjectState.php`, registered transitions): registers three distinct restore paths —
  - `ArchivedState → SubmittedState`
  - `ArchivedState → CollectingState`
  - `ArchivedState → ActiveState`
- **states.md / user-stories.md US-16**: each archive restores to the stage it was archived from (Récolte archive → `CollectingState`, En cours archive → `ActiveState`).

**Why it matters:** The code is already implemented one way. If the client actually wants all restorations to go to Proposition, the transition table, archive views, and restore UI all need to be rebuilt. This is a substantial change.

**Suggested next step:** Confirm with the client: should a Récolte-archived project restore into the Récolte queue, or re-enter Proposition? The current code assumes stage-specific restoration.

---

## Conflict B: "Project Manager" vs. "Chef de projet" — same role or two? — ✅ RESOLVED

**Resolution (2026-07-08):** Confirmed against `app/Enums/Role.php` and `database/seeders/RoleAndPermissionSeeder.php` — these are two distinct, real Spatie roles (`chef_de_projet` and `project_manager`), each seeded separately with its own permission set. Not an alias. See `docs/Source_of_truth.md` Roles table.

- **densified-summary.md Roles table** now has **two separate rows**:
  - `Chef de projet` — "Comment + update all En cours projects; not the proposer"
  - `Project Manager` — "Access everything, besides refusal acceptance and review on the direction page"
- **All other docs** (`states.md`, `user-stories.md`, `correlation.md`, `Notes.md`) use **only** "Chef de projet" / "project lead" / "responsable projet" — no separate "Project Manager" role.
- **Codebase** (`Project.php`): has a `leader_id` foreign key (chef de projet) — no separate "project manager" column or role.

**Why it matters:** If these are two different roles, a new role must be added to the permissions system and the `Project` model. If they are the same role with two names, one name must be standardized across all docs and code.

**Suggested next step:** Clarify with the team: is "Project Manager" a distinct role added after the client interviews, or is it just a translation/alias of "Chef de projet"? If the same, standardize to "Chef de projet" everywhere.

---

## Conflict C: When is the chef de projet assigned?

Three different answers appear across the docs:

- **densified-summary.md** (Open questions, answer row): *"when the project is validated then they can assign a chief"* — i.e. at Direction approval.
- **Notes.md** (line 4–5): *"a chief project is attributed to a project manager, when a project has enough resources → then look for a chef of project"* — i.e. at Récolte, once 80% resources found.
- **user-stories.md US-10**: *"The récolte manager can assign or confirm a chef de projet before launching"* — i.e. just before launch from Récolte to En cours.

**Why it matters:** The assignment moment determines when `projects.leader_id` is writeable and by whom. It also affects whether `leader_id` must be set before a project can move to En cours.

**Suggested next step:** Confirm the exact trigger: approved (Direction), 80% found (Récolte), or just-before-launch?

---

## Conflict D: 6-month Récolte checkpoint — warning or hard archive?

- **densified-summary.md** (open questions): confirms the 6-month rule is **real** (Answer: Yes).
- **thomas.md** (lines 98–100): *"in recolte → 6 months is the limit … less than 20% of the resources after 6 months"* — the word "limit" implies possible archiving at 6 months if <20%.
- **tiziano.md**: only mentions 12-month auto-archive; no 6-month threshold.
- **states.md / guide_mode_state.md**: only document the 12-month threshold; 6-month rule is noted as "unconfirmed."

**Why it matters:** The scheduled job logic is different depending on whether 6 months / <20% triggers:
  - (a) a warning/colour change only, or
  - (b) an early auto-archive (before the 12-month hard limit).

**Suggested next step:** Confirm with the client: at 6 months with <20% resources, does the project get archived, or just change colour / send a notification?
