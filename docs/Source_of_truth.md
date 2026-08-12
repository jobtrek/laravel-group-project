# Densified Summary — Project Tracking App

*Last updated 2026-07-08. Authoritative source — use this doc to resolve conflicts in other files.*
*Remaining unresolved items are marked ⚠️ and reference `docs/archive/clarification/conflicts-2026-06-25.md`.*

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
Proposition  →  Evaluation  →  Récolte  →  En cours
 (anyone)        (decide)      (launch)     (track)
     |               |              |            |
     +---------------+--------------+------------+→ Archivé (per stage)
                "le frigo" = refused-only archive
```

---

## Module 1 — Proposition

**State:** `PropositionState` (default — no DraftState; proposals are submitted immediately on creation)
- **Anyone** can propose a project.
- The **porteur** = the person proposing / guaranteeing the project (same person).
- The **project lead (chef de projet) is NOT the porteur** — two separate roles assigned separately.
- Direction members **cannot self-validate** their own proposals.

**Transitions:**
- `PropositionState` → `EvaluationState` (submitted for review)
- `RevisionState` → `PropositionState` (proposer resubmits after editing)

### Age-based colour coding

| Duration without activity | Colour |
|---------------------------|--------|
| +1 month | **Orange** |
| +2 months | **Red** |
| +3 months | **Auto-archived** |

### Proposal form fields

| Field | Notes |
|-------|-------|
| `titre` | Project title |
| `porteur` | Person proposing (auto-filled from logged-in user) |
| `description` | Problem + solution, max 3 paragraphs |
| `buts` | SMART goals, repeatable list |
| `perimetre` | What the project does and does not do — **required** |
| **Phases** (≥1 required) | `titre`, `duree`, `description`, `objectifs`, `livrables`, `ressources_necessaires` |
| **Impact evaluation** (required) | `portee` (0–50), `impact` (1–5), `confiance` (0–100%), `effort` (1–5) |

> Note: there is no `membres` field on the proposition form. Project membership is added later, during Récolte.

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

## Module 2 — Evaluation

**State:** `EvaluationState`

### Age-based colour coding

| Duration without activity | Colour |
|---------------------------|--------|
| +1 month | **Orange** |
| +2 months | **Red** |
| +3 months | **Auto-archived** |

- Direction sees all submitted proposals, **sorted by evaluation score descending**.
- **Three outcomes:** approve / refuse / request revision.
- **Only Direction** can write comments in this module.
- Direction members **cannot self-validate** their own proposals.

| Outcome | Next state | Notes |
|---------|-----------|-------|
| Approve | `RecolteState` | Project moves directly to Récolte |
| Refuse | `ArchiveState` ("le frigo") | Mandatory comment required; full form data preserved |
| Request revision | `RevisionState` | Proposer edits the **existing** form (not a new one) and resubmits |
---

## Module 3 — Récolte


**State:** `RecolteState`

### Age-based colour coding

| Duration without activity | Colour |
|---------------------------|--------|
| +1 month | **Orange** |
| +2 months | **Red** |
| +12 months | **Auto-archived** (exception — longer window than other modules) |

- Managed by the **récolte manager** — a different person than the porteur.
- **All people in Récolte can add/update resources — except the porteur.**
- Resource tracking per `Phase_Resource` row: `amount_needed` and `amount_found`.
- **Percentage** = `SUM(amount_found) / SUM(amount_needed)` across all phases.
- List is sortable/filterable by % resources found.
- Budget % entry is **manual** (no payment system integration).

| Trigger | Action |
|-----------|--------|
| Chef de projet launches the project (requires `leader_id` set and at least 80% resources found) | Moves to `EncoursState` — **manual action with service-level gate** |
| 12 months elapsed | Auto-archive → `ArchiveState` |

### Chef de projet assignment

The Chef de projet is assigned when the project is validated by Direction in the Récolte area.

---

## Module 4 — En cours

**State:** `EncoursState`

### Age-based colour coding

| Duration without comment | Colour |
|--------------------------|--------|
| +1 month | **Orange** → Email #1 to chef de projet |
| +2 months | **Red** → Email #2 CC'd to follow-up role |
| +3 months | **Auto-archived** |

- Shows per project: `titre`, `description`, budget, **who works on it**, resources found.
- **Comment log** (historical): person name, date, content — reverse-chronological.
- Comments by **chef de projet** primarily; collaborateurs may also comment.
- Comments **cannot be edited or deleted** after posting.
- Any comment **resets the inactivity clock**.
- Chef de projet marks project **Completed** (`CompleteState`) — final mandatory comment required; project moves from En cours dashboard to **Archivé dashboard**.

### Inactivity escalation

| Delay | Action |
|-------|--------|
| 1 month no comment | Email #1 → chef de projet; `last_reminder_at` set |
| +1 week, still no comment | Email #2 (stronger) — CC'd to all users with follow-up role |
| 3 months no comment | Auto-archive → `ArchiveState` |

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
| Proposition archive | Proposition | 3 months in `PropositionState` / `RevisionState` |
| Le frigo | Direction | Manual refusal; full form fields preserved |
| Récolte archive | Récolte | 12 months in `RecolteState` |
| En cours archive | En cours | 3 months no comment, or manual completion (`CompleteState`) |

- **Everyone** can see le frigo and all stage archives.
- Archive retention: **1-year hard limit** — not restored within 1 year → permanently deleted.
- All projects restored from archive will go back into proposition no matter their stage

---

## Roles

- **Multiple roles per person are allowed** (a user can be collaborateur + direction, etc.).
- Role assignment: **admin only**.
- Default role on account creation: **collaborateur**.

| Role | Key permissions |
|------|----------------|
| **Collaborateur** | Propose projects; edit own proposals; see all projects |
| **Direction** | Approve / refuse / request revision on proposals; comment in Evaluation module; see all; archive projects |
| **Récolte Manager** | Add/update resources on Récolte projects; see all |
| **Project Manager** | Distinct role from Chef de projet (`App\Enums\Role::ProjectManager`, seeded separately in `RoleAndPermissionSeeder`); can archive projects and send projects to Direction; cannot evaluate proposals |
| **Chef de projet** | Comment on all En cours projects; launch projects from Récolte; mark complete; see all |
| **Admin** | Full access; only role that can assign/change roles |

> "Project Manager" and "Chef de projet" are two distinct, real roles in code — not aliases of each other — each with its own permission set. Confirmed against `app/Enums/Role.php` and `database/seeders/RoleAndPermissionSeeder.php`.

---

## State machine — canonical reference

**Codebase location:** `app/Models/States/` (source of truth for class names)

Class names are **unaccented ASCII**; accents only appear in the stored `$name` value and the human-readable `label()`, never in the class identifier.

| Class file | State name (stored value) | Label | Stage |
|-----------|-----------|-------|-------|
| `PropositionState.php` | `proposition` | Proposition | Proposition |
| `EvaluationState.php` | `évaluation` | Évaluation | Evaluation (Direction review) |
| `RevisionState.php` | `révision` | Révision | Revision (sent back to proposer) |
| `RecolteState.php` | `récolte` | Récolte | Récolte |
| `EncoursState.php` | `en cours` | En cours | En cours |
| `CompleteState.php` | `complété` | Complété | Terminal (moves to Archivé dashboard) |
| `ArchiveState.php` | `archivé` | Archivé | All archives (le frigo + stage archives) |

> **No `DraftState`** — default is `PropositionState`. Proposals are submitted immediately on creation.
> **No `ReadyState`** — moving from `RecolteState` to `EncoursState` remains a manual launch action. The 80% threshold is enforced in `ProjectService::moveToEncours()` (service-level gate), not as an automatic transition.
> **No `SuspendedState`** — the correct class is `RevisionState`. Update any doc or code that references `SuspendedState`.

### Registered transitions (from codebase)

| From | To | Actor |
|------|----|-------|
| `PropositionState` | `EvaluationState` | Proposer (submits for review) |
| `EvaluationState` | `RecolteState` | Direction (approve) |
| `EvaluationState` | `ArchiveState` | Direction (refuse — le frigo) |
| `EvaluationState` | `RevisionState` | Direction (request revision) |
| `RevisionState` | `PropositionState` | Proposer (resubmits after editing) |
| `PropositionState` | `ArchiveState` | System (3 months inactive) |
| `RevisionState` | `ArchiveState` | System (3 months inactive) |
| `RecolteState` | `EncoursState` | Chef de projet — manual launch, requires `leader_id` and `SUM(amount_found) / SUM(amount_needed) >= 0.8` (service-level gate) |
| `RecolteState` | `ArchiveState` | System (12 months elapsed) |
| `EncoursState` | `CompleteState` | Chef de projet (moves to Archivé dashboard) |
| `EncoursState` | `ArchiveState` | System (3 months no comment) |
| `ArchiveState` | `PropositionState` | Direction / Admin (restore) |

---

## Answered questions (resolved since first draft)

| Question | Answer |
|----------|--------|
| DraftState — auto or manual submit? | **Auto-submit; no DraftState** |
| SuspendedState or ModificationState? | **ModificationState** (class exists on disk) |
| Proposer edits existing form or new one on revision? | **Edits the existing form** |
| Multiple roles per person allowed? | **Yes** |
| Is validator Direction or RH? | **Direction — there is no separate RH role** |
| All roles can see all projects? | **Yes** 
| Who can access le frigo and archives? | **Everyone** |
| Full form fields preserved in le frigo? | **Yes** |
| Archive retention before deletion? | **1 year** |
| Completed projects after 1 year? | **Deleted** |
| Can direction self-validate? | **No** |
| 6-month Récolte warning real? | **Yes** (action still unconfirmed — see Conflict D) |
| Budget % entry — manual or automatic? | **Manual** |
| Multiple chefs de projet per project? | **Yes** |
| Accounts — self-register or admin-created? | **Admin-created** |
