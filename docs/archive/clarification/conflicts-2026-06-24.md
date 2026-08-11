# Clarification — Conflicts Found (2026-06-24)

---
### SOLVED
## Conflict 1: State name — "ModificationState" vs "SuspendedState"

- **File A** (`guide_mode_state.md`, transition table and state map): uses **`ModificationState`** for the state where a project is sent back to the proposer for revision. It appears throughout the allowed-transitions table, auto-archive rules, and the state map diagram.
- **File B** (`states.md`, `user-stories.md`): calls the same state **`SuspendedState`** (Laravel class `App\States\Project\SuspendedState`).

**Why it matters:** The actual Laravel class name in the codebase must be one or the other. If these two docs disagree, one set of code references is wrong. Anyone implementing or reviewing the state machine will build against the wrong class name.

**Suggested next step:** Check `app/States/Project/` (or `app/Models/States/`) for the actual file on disk. Whichever class exists is canonical; update the other doc.

---
### SOLVED

## Conflict 2: Récolte colour-coding and timing thresholds

- **File A** (`Notes.md`, lines 9–12): says **"no récolte for 3 months → orange / no update for 2 months → red"** and for En cours **"after 1 month → turns red → archive"** — i.e. only two colour steps in récolte (3 mo orange, 2 mo red) and En cours archives at 1 month.
- **File B** (`thomas.md`, `tiziano.md`, `states.md`, `summary.md`, `correlation.md` item 21): consistent across all — **+1 month orange / +2 months red / +3 months auto-archive** for all stages including récolte (with a longer 12-month hard archive for récolte).

**Why it matters:** The scheduler and colour-coding logic will be built from one set of numbers. If `Notes.md` reflects a later client correction, the other files are outdated. If `Notes.md` is a misheard note, it's wrong.

**Suggested next step:** Confirm with the client (or the person who wrote `Notes.md`) whether the thresholds are 1/2/3 months (as in all other files) or the variant in `Notes.md`. This was a post-contact note — it may supersede earlier values.

---
### SOLVED

## Conflict 3: Archive restoration target state

- **File A** (`Notes.md`, line 17): **"projects come back from archived → go back to proposition"** — implies ALL archived projects restore to the Proposition stage, regardless of which stage they were archived from.
- **File B** (`states.md` restoration table, `user-stories.md` US-16, `guide_mode_state.md` transition table): archived projects restore **to the stage they were archived from** — Récolte archive → `CollectingState`, En cours archive → `ActiveState`, etc.

**Why it matters:** If `Notes.md` is correct, the restoration logic is flat (always back to Proposition). If the other files are correct, each stage archive has its own restoration target. These are two fundamentally different UX flows and DB operations.

**Suggested next step:** This is likely a post-contact client clarification — confirm which interpretation `Notes.md` intended. Ask the person who wrote it.

---
### SOLVED

## Conflict 4: Who can validate/refuse proposals — Direction vs. RH

- **File A** (`tiziano.md`, line 156): **"Seul les RH peuvent valider et refuser les projets. Ou suspendre."** — only HR can validate/refuse.
- **File B** (`thomas.md`, `states.md`, `user-stories.md`, `summary.md`, `correlation.md`): attribute approval/refusal to **Direction** (not specifically HR).

**Why it matters:** The `ProjectPolicy` approve/refuse methods guard against a specific role. If it's HR, that role must exist separately from Direction. If they are the same body, only one role is needed.

**Suggested next step:** Ask the client: is "RH" a separate role from "Direction", or is "Direction" the umbrella term that includes RH? (`correlation.md` Discrepancy A already flags this.)

---
### SOLVED

## Conflict 5: Multiple roles per person

- **File A** (`thomas.md`, line 118 + `correlation.md` confirmed item 24): **multiple roles are allowed** — a person can be both collaborateur and direction.
- **File B** (`tiziano.md`, line 164): **"Personne peut avoir plusieurs rôles"** — ambiguous in French: means either "a person can have multiple roles" (agrees) OR "nobody can have multiple roles" (contradicts).

**Why it matters:** The roles and permissions model is fundamentally different depending on the answer. If exclusive roles, the middleware must prevent role stacking. If multiple roles, it must support them.

**Suggested next step:** Confirm directly with the client in writing. (`correlation.md` Discrepancy B already flags this as unresolved.)

---
### SOLVED


## Conflict 6: DraftState — manual submit vs. auto-submit on creation

- **File A** (`nikita.md`, Stage 1): **"Once created, the project is automatically submitted for administrative review"** — no draft state, submission is automatic.
- **File B** (`states.md`, `user-stories.md` US-01/US-02, `guide_mode_state.md`): there is an explicit **`DraftState`** where the proposer can save without submitting, and submission is a **manual action**.

**Why it matters:** US-02 (save as draft) only makes sense if submission is manual. The DB schema has a `DraftState`. Auto-submit would eliminate the draft workflow entirely.

**Suggested next step:** `nikita.md` appears to be an earlier or simplified interpretation. `states.md` and `user-stories.md` are the more detailed and consistent source. Treat auto-submit as **incorrect** unless the client confirms otherwise.

---
### SOLVED


## Conflict 7: Visibility scope — "all roles can view all projects" vs. scoped visibility

- **File A** (`nikita_notes_2`, Roles section): **"All roles can view all projects."**
- **File B** (`states.md` per-state visibility, `user-stories.md`, `summary.md`): visibility is scoped — drafts visible only to proposer; Récolte visible to récolte manager + chef + direction; etc.

**Why it matters:** Visibility gates in `ProjectPolicy` and all index/show views depend on this. "All roles see all" is a fundamentally simpler permission model.

**Suggested next step:** `nikita_notes_2` is a simplified summary. `states.md` is the detailed per-state reference with source citations. Treat scoped visibility as correct; note that `nikita_notes_2` may have been written before the scoped model was defined. Confirm with the client if there is any doubt.

---
### SOLVED

## Conflict 8: Récolte 6-month / <20% warning threshold

- **File A** (`thomas.md`, lines 98–100): **"in récolte → 6 months is the limit … less than 20% of the resources after 6 months"** — implies an intermediate 6-month/20% warning before the 12-month archive.
- **File B** (`tiziano.md`, `states.md`): **no 6-month threshold mentioned**. Tiziano only says "after 1 year, auto-archive."

**Why it matters:** If the 6-month/20% rule is real, a separate scheduled check and possible alert must be implemented. If it isn't, it's dead code.

**Suggested next step:** Ask the client whether there is a 6-month checkpoint in Récolte. (`correlation.md` Discrepancy D + `states.md` State 6 already flag this as unconfirmed.)
