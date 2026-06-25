# Densified Summary — Project Tracking App

*Last updated 2026-06-25. Authoritative source — use this doc to resolve conflicts in other files.*
*Remaining unresolved items are marked ⚠️ and reference `clarification/conflicts-2026-06-25.md`.*

---

## What the app is

- Internal **project tracking / follow-up app** — NOT a management tool.
- **Portfolio view**: shows projects, who works on them, budgets — no per-project to-do list.
- Visual design: **sober / minimal**.
- Client: a foundation with ~35 collaborators. All projects are **internally proposed**.
- Accounts are **admin-created** — no self-registration.

---

## The 4-module lifecycle

```
Proposition  →  Direction  →  Récolte  →  En cours
 (anyone)        (decide)       (≥80%)      (track)
     |               |              |            |
     +---------------+--------------+------------+→ Archives (per stage)
                "le frigo" = refused-only archive
```

---

## Module 1 — Proposition

**State:** `SubmittedState` (default — no DraftState; proposals are submitted immediately on creation)

- **Anyone** can propose a project.
- The **porteur** = the person proposing / guaranteeing the project (same person).
- The **project lead (chef de projet) is NOT the porteur** — two separate roles assigned separately.
- Direction members **cannot self-validate** their own proposals.

### Proposal form fields

| Field | Notes |
|-------|-------|
| `titre` | Project title |
| `porteur` | Person proposing (auto-filled from logged-in user) |
| `membres` | All people involved (multi-select) |
| `description` | Problem + solution, max 3 paragraphs |
| `buts` | SMART goals, repeatable list |
| `perimetre` | What the project does and does not do — optional for small projects |
| **Phases** (≥1 required) | `titre`, `duree`, `description`, `objectifs`, `livrables`, `ressources_necessaires` |
| **Impact evaluation** (required) | `portee` (0–50), `impact` (1–5), `confiance` (0–100%), `effort` (1–5) |

### Impact evaluation scales

**Portée** (0–50): direct collaborators = 1pt each, partners = 0.5pt each.

**Impact** (1–5):
| Score | Meaning |
|-------|---------|
| 1 | Negligible — concerns only a few roles |
| 2 | Limited — one product or circle, not lasting |
| 3 | New "capacity" for the foundation — several products/circles over time |
| 4 | Significantly changes how circles/products operate — durable advantage |
| 5 | Transformational — foundation enters a new era, decade-long change |

**Effort** (1–5):
| Score | Meaning |
|-------|---------|
| 1 | ≤1 week ETP, <200 CHF |
| 2 | 1–4 weeks ETP, <2 000 CHF |
| 3 | 1–3 months ETP, 2 000–10 000 CHF |
| 4 | 3–6 months ETP, 10 000–50 000 CHF |
| 5 | Multi-year >100% ETP, >50 000 CHF |


---

## Module 2 — Direction

**State:** `SubmittedState`

- Direction sees all submitted proposals, **sorted by evaluation score descending**.
- **Three outcomes:** approve / refuse / request revision.
- **Only Direction** can write comments in this module.
- Direction members **cannot self-validate** their own proposals.

| Outcome | Next state | Notes |
|---------|-----------|-------|
| Approve | `ApprovedState` → auto-advances to `CollectingState` | System moves immediately |
| Refuse | `RefusedState` ("le frigo") | Mandatory comment required; full form data preserved |
| Request revision | `ModificationState` | Proposer edits the **existing** form (not a new one) and resubmits |

---

## Module 3 — Récolte

**States:** `CollectingState` / `ReadyState`

- Managed by the **récolte manager** — a different person than the porteur.
- **All people in Récolte can add/update resources — except the porteur.**
- Resource tracking per `Phase_Resource` row: `amount_needed` and `amount_found`.
- **Percentage** = `SUM(amount_found) / SUM(amount_needed)` across all phases.
- List is sortable/filterable by % resources found.
- Budget % entry is **manual** (no payment system integration).

| Threshold | Action |
|-----------|--------|
| ≥ 80% resources found | Project turns **green** → `ReadyState` → can be launched to En cours |
| Resources drop below 80% after reaching ReadyState | Reverts to `CollectingState` |
| 12 months elapsed | Auto-archive → `ArchivedState` |

### Chef de projet assignment

The Chef du projet is assigned when the person validates the project from direction. in the recolte area.

---

## Module 4 — En cours

**State:** `ActiveState`

- Shows per project: `titre`, `description`, budget, **who works on it**, resources found.
- **Comment log** (historical): person name, date, content — reverse-chronological.
- Comments by **chef de projet** primarily; collaborateurs may also comment.
- Comments **cannot be edited or deleted** after posting.
- Any comment **resets the inactivity clock**.
- Chef de projet marks project **Completed** (`CompletedState`) — final mandatory comment required.

### Inactivity escalation

| Delay | Action |
|-------|--------|
| 1 month no comment | Email #1 → chef de projet; `last_reminder_at` set |
| +1 week, still no comment | Email #2 (stronger) — CC'd to all users with follow-up role |
| 3 months no comment | Auto-archive → `ArchivedState` |

---

## Cross-cutting rules

### Age-based colour coding (all stages)

| Duration | Status |
|----------|--------|
| +1 month without activity | **Orange** |
| +2 months | **Red** |
| +3 months | **Auto-archived** |

Exception — Récolte: auto-archive at **12 months** (not 3).

### Archives — four separate buckets

| Archive | Stage | Auto-archive trigger |
|---------|-------|---------------------|
| Proposition archive | Proposition | 3 months in `SubmittedState` / `ModificationState` |
| Le frigo | Direction | Manual refusal; full form fields preserved |
| Récolte archive | Récolte | 12 months in `CollectingState` / `ReadyState` |
| En cours archive | En cours | 3 months no comment, or manual completion |

- **Everyone** can see le frigo and all stage archives.
- Archive retention: **1-year hard limit** — not restored within 1 year → permanently deleted.
- ⚠️ UNRESOLVED — restoration target: densified-summary says all go back to Proposition; codebase registers stage-specific restoration (Récolte→`CollectingState`, En cours→`ActiveState`). See `clarification/conflicts-2026-06-25.md` Conflict A.

---

## Roles

- **Multiple roles per person are allowed** (a user can be collaborateur + direction, etc.).
- Role assignment: **admin only**.
- Default role on account creation: **collaborateur**.

| Role | Key permissions |
|------|----------------|
| **Collaborateur** | Propose projects; edit own proposals; see all projects |
| **Direction** | Approve / refuse / suspend proposals; comment in Direction module; see all |
| **Récolte Manager** | Add/update resources on Récolte projects; see all |
| **Chef de projet** | Comment on all En cours projects; launch projects from Récolte; mark complete; see all |
| **Admin** | Full access; only role that can assign/change roles |

> Note: `densified-summary.md` previously listed "Project Manager" as a separate role. ⚠️ This may be an alias for "Chef de projet" — see `clarification/conflicts-2026-06-25.md` Conflict B.

---

## State machine — canonical reference

**Codebase location:** `app/Models/States/` (source of truth for class names)

| Class file | State name | Stage |
|-----------|-----------|-------|
| `SubmittedState.php` | `submitted` | Proposition / Direction |
| `ModificationState.php` | `modification` | Revision (sent back to proposer) |
| `ApprovedState.php` | `approved` | Transient (auto-advances to Collecting) |
| `RefusedState.php` | `refused` | Le frigo |
| `CollectingState.php` | `collecting` | Récolte |
| `ReadyState.php` | `ready` | Récolte (≥80%) |
| `ActiveState.php` | `active` | En cours |
| `CompletedState.php` | `completed` | Terminal |
| `ArchivedState.php` | `archived` | All stage archives |

> **No `DraftState`** — default is `SubmittedState`. Proposals are submitted immediately on creation.
> **No `SuspendedState`** — the correct class is `ModificationState`. Update any doc or code that references `SuspendedState`.

### Registered transitions (from codebase)

| From | To | Actor |
|------|----|-------|
| `SubmittedState` | `ApprovedState` | Direction |
| `SubmittedState` | `RefusedState` | Direction |
| `SubmittedState` | `ModificationState` | Direction |
| `SubmittedState` | `ArchivedState` | System (3 months) |
| `ModificationState` | `SubmittedState` | Proposer (after editing) |
| `ModificationState` | `ArchivedState` | System (3 months) |
| `ApprovedState` | `CollectingState` | System (auto) |
| `RefusedState` | `SubmittedState` | Direction / Admin (restore from frigo) |
| `CollectingState` | `ReadyState` | System (≥80%) |
| `CollectingState` | `ArchivedState` | System (12 months) |
| `ReadyState` | `ActiveState` | Récolte Manager / Chef de projet |
| `ReadyState` | `CollectingState` | System (drops below 80%) |
| `ReadyState` | `ArchivedState` | System (12 months) |
| `ActiveState` | `CompletedState` | Chef de projet |
| `ActiveState` | `ArchivedState` | System (3 months no comment) |
| `ArchivedState` | `SubmittedState` | Direction / Admin |
| `ArchivedState` | `CollectingState` | Récolte Manager / Admin |
| `ArchivedState` | `ActiveState` | Chef de projet / Admin |

---

## Answered questions (resolved since first draft)

| Question | Answer |
|----------|--------|
| DraftState — auto or manual submit? | **Auto-submit; no DraftState** |
| SuspendedState or ModificationState? | **ModificationState** (class exists on disk) |
| Proposer edits existing form or new one on revision? | **Edits the existing form** |
| Multiple roles per person allowed? | **Yes** |
| Is validator Direction or RH? | **Direction — there is no separate RH role** |
| All roles can see all projects? | **Yes** |
| Who can access le frigo and archives? | **Everyone** |
| Full form fields preserved in le frigo? | **Yes** |
| Archive retention before deletion? | **1 year** |
| Completed projects after 1 year? | **Deleted** |
| Can direction self-validate? | **No** |
| 6-month Récolte warning real? | **Yes** (action still unconfirmed — see Conflict D) |
| Budget % entry — manual or automatic? | **Manual** |
| Multiple chefs de projet per project? | **Yes** |
| Accounts — self-register or admin-created? | **Admin-created** |
