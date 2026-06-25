# User Stories — Project Tracking App

Organized by lifecycle stage. Each story includes the **Laravel model state class**
(`spatie/laravel-model-states` convention) and the allowed **transitions** out of it.

---

## State Machine Overview

```
DraftState
  └─► SubmittedState
        ├─► SuspendedState ──► SubmittedState   (revision loop)
        ├─► RefusedState                         (le frigo)
        └─► ApprovedState
              └─► CollectingState
                    └─► ReadyState
                          └─► ActiveState
                                └─► CompletedState

Any state ──► ArchivedState (age-based or manual)
ArchivedState ──► [original stage state] (restoration)
```

---

## Roles

| Role | Scope |
|------|-------|
| **Collaborateur** | Propose and edit their own projects |
| **Direction / RH** | Approve, refuse, or suspend proposals |
| **Récolte Manager** | Track resource gathering on approved projects |
| **Chef de projet** | Comment on and update all in-progress projects |
| **Admin** | Full access; assigns roles |

> A user may hold multiple roles simultaneously (e.g. collaborateur + chef de projet).
> Role assignment is done by the Admin only.

---

## Module 1 — Proposition

### US-01 — Submit a project proposal
**State:** `App\States\Project\DraftState`
**Transitions allowed:** → `SubmittedState`

> **As a** collaborateur,
> **I want to** fill out and submit a project proposal form,
> **so that** the direction can review it.

**Acceptance criteria:**
- The form includes: `titre`, `porteur`, `membres`, `description`, `buts`, `perimetre` (optional), and at least one `Phase`.
- Each phase requires: `titre`, `duree`, `description`, `objectifs`, `livrables`, `ressources_necessaires`.
- A total-resources summary (`ressources_totales`) is shown only when the phase count exceeds 2.
- The evaluation section (impact matrix) must be completed before submission: `portee` (0–50), `impact` (1–5), `confiance` (0–100%), `effort` (1–5).
- On submit, the project state transitions from `DraftState` → `SubmittedState`.
- A project in `DraftState` is only visible to its proposer.

---

### US-02 — Save a proposal as draft
**State:** `App\States\Project\DraftState`
**Transitions allowed:** stays in `DraftState`

> **As a** collaborateur,
> **I want to** save my proposal without submitting it,
> **so that** I can complete it later.

**Acceptance criteria:**
- The form can be saved at any point, even incomplete.
- Draft projects appear in the proposer's personal dashboard only.
- No validation is run on save — only on submission.

---

### US-03 — Revise a suspended proposal
**State:** `App\States\Project\SuspendedState`
**Transitions allowed:** → `SubmittedState`

> **As a** collaborateur,
> **I want to** update my proposal after the direction requested clarification,
> **so that** it can be re-evaluated.

**Acceptance criteria:**
- The proposer can see the direction's comment explaining what needs to change.
- The proposer can edit all proposal fields while in `SuspendedState`.
- On resubmit, the state transitions `SuspendedState` → `SubmittedState`.
- The project re-enters the direction queue, sorted by evaluation score.

---

## Module 2 — Direction

### US-04 — View and sort proposals by impact
**State:** `App\States\Project\SubmittedState`
**Transitions allowed:** → `ApprovedState` | `RefusedState` | `SuspendedState`

> **As a** direction member,
> **I want to** see all submitted proposals sorted by their impact score,
> **so that** I can prioritize the most impactful projects.

**Acceptance criteria:**
- The direction view lists all projects in `SubmittedState`.
- Default sort is by evaluation score descending (calculated from `portee`, `impact`, `confiance`, `effort`).
- The list is filterable by score, date, and proposer.
- Projects older than 1 month are highlighted in **orange**.
- Projects older than 2 months are highlighted in **red**.
- Projects older than 3 months are automatically transitioned to `ArchivedState`.

---

### US-05 — Approve a proposal
**State:** `App\States\Project\SubmittedState`
**Transitions allowed:** → `ApprovedState`

> **As a** direction member,
> **I want to** approve a submitted proposal,
> **so that** it moves to the récolte phase for resource gathering.

**Acceptance criteria:**
- Approval transitions the state `SubmittedState` → `ApprovedState` → `CollectingState`.
- The project immediately appears in the Récolte module.
- The direction can optionally attach a comment before approving.

---

### US-06 — Refuse a proposal (le frigo)
**State:** `App\States\Project\SubmittedState`
**Transitions allowed:** → `RefusedState`

> **As a** direction member,
> **I want to** refuse a submitted proposal,
> **so that** it is archived in "le frigo" and the proposer is notified.

**Acceptance criteria:**
- Refusal transitions `SubmittedState` → `RefusedState`.
- The project moves to the **Direction archive ("le frigo")** — separate from other stage archives.
- A mandatory comment field must be filled before refusing.
- The proposer is notified (notification or email).
- A refused project can be restored from le frigo back to `SubmittedState` by the direction.

---

### US-07 — Suspend a proposal (request revision)
**State:** `App\States\Project\SubmittedState`
**Transitions allowed:** → `SuspendedState`

> **As a** direction member,
> **I want to** suspend a proposal and ask the proposer for more information,
> **so that** we can make a proper decision once the proposal is complete.

**Acceptance criteria:**
- Suspension transitions `SubmittedState` → `SuspendedState`.
- A mandatory comment must be written explaining what information is missing.
- The proposer is notified and can now edit and resubmit (see US-03).
- Suspended projects are shown in a dedicated "En révision" sub-list within the direction view.

---

### US-08 — Write a comment on a proposal
**State:** `App\States\Project\SubmittedState` | `SuspendedState`

> **As a** direction member,
> **I want to** add a comment to a proposal,
> **so that** the team has a record of the discussion.

**Acceptance criteria:**
- Comments are stored with: author name, timestamp, content, and current stage.
- Comments are read-only after submission.
- Comments are visible to the direction and the proposer.

---

## Module 3 — Récolte

### US-09 — Track resource gathering
**State:** `App\States\Project\CollectingState`
**Transitions allowed:** → `ReadyState` (auto, when ≥80%) | `ArchivedState` (auto, after 12 months)

> **As a** récolte manager,
> **I want to** record resources and funds found for each project phase,
> **so that** I can track progress toward the 80% threshold.

**Acceptance criteria:**
- The récolte view lists all projects in `CollectingState`, sortable by % resources found.
- For each project, the manager sees each phase with its `ressources_necessaires` lines.
- The manager can update `amount_found` on each `Phase_Resource` row.
- The system calculates `SUM(amount_found) / SUM(amount_needed)` across all phases.
- When the percentage reaches **≥ 80%**, the project automatically transitions `CollectingState` → `ReadyState` and turns **green**.
- Below 80%, the project stays in `CollectingState`.
- Same age-based colour rules apply: **orange** at 1 month, **red** at 2 months.
- Auto-archive at **12 months** (not 3), regardless of resource percentage.

---

### US-10 — Move a ready project to En cours
**State:** `App\States\Project\ReadyState`
**Transitions allowed:** → `ActiveState`

> **As a** récolte manager or chef de projet,
> **I want to** move a project that has reached 80% resources into "En cours",
> **so that** work on the project can begin.

**Acceptance criteria:**
- Projects in `ReadyState` are displayed in green in the récolte list.
- A "Lancer le projet" action transitions `ReadyState` → `ActiveState`.
- The project immediately appears in the En cours module.
- The récolte manager can assign or confirm a `chef de projet` before launching.

---

## Module 4 — En cours

### US-11 — View active projects
**State:** `App\States\Project\ActiveState`

> **As a** chef de projet,
> **I want to** see all active projects with their details,
> **so that** I can monitor progress across the portfolio.

**Acceptance criteria:**
- The En cours view shows: `titre`, `description`, `budget_global`, members, resources found, and the comment log.
- The comment log shows entries in reverse-chronological order: author name, timestamp, content.
- All users can view En cours projects (read-only); only the chef de projet and collaborators can comment.

---

### US-12 — Post a progress comment
**State:** `App\States\Project\ActiveState`

> **As a** chef de projet (or collaborateur),
> **I want to** post a progress comment on an active project,
> **so that** the team has an up-to-date history of the project's advancement.

**Acceptance criteria:**
- Any comment resets the inactivity clock for email escalation.
- Comments are stored with: `id_user`, `created_at`, `content`, `stage = 'en_cours'`.
- Commenting is primarily for the chef de projet; collaborateurs may also comment.
- Comments cannot be edited or deleted after posting.

---

### US-13 — Receive inactivity reminder (email escalation)
**State:** `App\States\Project\ActiveState`

> **As a** chef de projet,
> **I want to** receive an automatic reminder when I haven't updated a project for a month,
> **so that** the project tracking stays current.

**Acceptance criteria:**
- If no comment is posted in **30 days**, a first reminder email is sent to the chef de projet. `last_reminder_at` is set.
- If no comment is posted within **7 days after the first email**, a second, stronger email is sent — CC'd to all users with a follow-up role on the project.
- If still no activity after **3 months**, the project is transitioned to `ArchivedState`.
- A new comment at any point resets the clock and clears the escalation.

---

### US-14 — Mark a project as completed
**State:** `App\States\Project\ActiveState`
**Transitions allowed:** → `CompletedState`

> **As a** chef de projet,
> **I want to** mark an active project as completed,
> **so that** it is closed out and archived correctly.

**Acceptance criteria:**
- A "Terminer le projet" action transitions `ActiveState` → `CompletedState`.
- Completed projects move to the En cours archive.
- A final mandatory comment must be written before marking as complete.
- Completed projects are read-only.

---

## Archives

### US-15 — View stage archives
**State:** `App\States\Project\ArchivedState`

> **As any** authenticated user,
> **I want to** browse the archive for each stage,
> **so that** I can look up past or stalled projects.

**Acceptance criteria:**
- Each stage has its own archive section: Proposition archive, le Frigo (Direction), Récolte archive, En cours archive.
- The archive shows the stage the project was in when archived, and the reason (age / refused / manual).
- Projects in the archive are read-only.
- Projects archived for more than **1 year** are permanently locked (no restoration).

---

### US-16 — Restore an archived project
**State:** `App\States\Project\ArchivedState`
**Transitions allowed:** → state matching the stage it was archived from

> **As a** direction member or admin,
> **I want to** restore an archived project back to its previous stage,
> **so that** work on it can resume.

**Acceptance criteria:**
- Restoration is only available within the 1-year archive window.
- The project is restored to the `current_stage` it had before archiving.
- The `archived_at` column is cleared and `restored_at` is set.
- The inactivity clock resets on restoration.

---

## Admin

### US-17 — Assign roles to users
> **As an** admin,
> **I want to** assign or change a user's role,
> **so that** they have the correct permissions in the app.

**Acceptance criteria:**
- Default role on account creation is `collaborateur`.
- Available roles: `collaborateur`, `direction`, `recolte_manager`, `chef_de_projet`, `admin`.
- A user can hold multiple roles simultaneously.
- Role changes take effect immediately without requiring re-login.

---

## Laravel State Classes — Reference

```
app/States/Project/
├── ProjectState.php          (abstract base)
├── DraftState.php
├── SubmittedState.php
├── SuspendedState.php
├── ApprovedState.php
├── RefusedState.php
├── CollectingState.php
├── ReadyState.php
├── ActiveState.php
├── CompletedState.php
└── ArchivedState.php
```

### Allowed transitions (spatie/laravel-model-states)

| From | To | Actor |
|------|----|-------|
| `DraftState` | `SubmittedState` | Collaborateur |
| `SubmittedState` | `ApprovedState` | Direction |
| `SubmittedState` | `RefusedState` | Direction |
| `SubmittedState` | `SuspendedState` | Direction |
| `SuspendedState` | `SubmittedState` | Collaborateur (after revision) |
| `ApprovedState` | `CollectingState` | System (auto on approval) |
| `CollectingState` | `ReadyState` | System (auto at ≥80%) |
| `ReadyState` | `ActiveState` | Récolte Manager / Chef de projet |
| `ActiveState` | `CompletedState` | Chef de projet |
| `*` | `ArchivedState` | System (age-based) / Direction / Admin |
| `ArchivedState` | `SubmittedState` \| `CollectingState` \| `ActiveState` | Direction / Admin |
