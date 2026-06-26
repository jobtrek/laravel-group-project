# Project States — Full Reference

Every state the app needs to implement, with the exact source note and line (or
section) where the requirement originates, and the transitions between states.

---

## State Overview

```
┌─────────────────────────────────────────────────────────────────┐
│  DraftState                                                     │
│    │ proposer submits                                           │
│    ▼                                                            │
│  SubmittedState  ──── direction refuses ────► RefusedState      │
│    │                                          (le frigo)        │
│    │ direction suspends                                         │
│    ▼                                                            │
│  SuspendedState                                                 │
│    │ proposer revises and resubmits                             │
│    ▼                                                            │
│  SubmittedState  ──── direction approves ───► CollectingState   │
│                                                │                │
│                                         ≥ 80% resources         │
│                                                ▼                │
│                                          ReadyState             │
│                                                │                │
│                                         manually launched       │
│                                                ▼                │
│                                          ActiveState            │
│                                                │                │
│                                         manually closed         │
│                                                ▼                │
│                                          CompletedState         │
│                                                                 │
│  Any state ────────────────────────────────► ArchivedState      │
│  ArchivedState ──── restored ──────────────► [original state]   │
└─────────────────────────────────────────────────────────────────┘
```

---

## State 1 — DraftState

**Laravel class:** `App\States\Project\DraftState`
**Stage:** Proposition
**Visible to:** proposer only

### What it is
A project that has been started but not yet submitted. The proposer can freely
edit all fields. No validation is applied until submission.

### Source references

| Source | Location | Quote / note |
|--------|----------|--------------|
| `nikita.md` | Line 109 | `"Draft →"` — first state in the recommended status chain |
| `nikita_notes_2` | Roles section | *"A user is created with a default role: regular user / collaborator"* — implies the first action a collaborator takes is creating a draft |
| `proposition.md` | Frontend notes | *"Phases are the most complex UI element — use a dynamic add phase / remove phase pattern"* — describes the form being filled incrementally |

### Entry conditions
- A collaborateur starts a new proposal.

### Exit conditions (transitions out)

| Transition | Target state | Trigger |
|------------|-------------|---------|
| Proposer submits the completed form | `SubmittedState` | Manual action by proposer |

### Rules while in this state
- Only the proposer can see or edit it.
- No age-based colour coding or archiving applies — a draft has no deadline.
- The form requires at minimum: `titre`, `description`, `buts`, at least one phase, and a completed evaluation section before submission is allowed (`proposition.md`, section headers).

---

## State 2 — SubmittedState

**Laravel class:** `App\States\Project\SubmittedState`
**Stage:** Direction
**Visible to:** Direction / RH, proposer (read-only), chef de projet (read-only)

### What it is
A proposal that has been submitted and is waiting for a direction decision. This
is the main queue the direction works from. Projects are sorted by evaluation
score descending.

### Source references

| Source | Location | Quote / note |
|--------|----------|--------------|
| `thomas.md` | Line 46–48 | *"analyse all the proposition of project what is the impact / trier -> so filtering via impact"* |
| `tiziano.md` | Direction section | *"Liste tous les projets et trie par impact"* |
| `correlation.md` | Item 12 (line 72–74) | Confirmed: direction lists and sorts by impact |
| `thomas.md` | Line 49–50 | *"they decide, we are going to do it or not, or then you can we ask for more details"* |
| `tiziano.md` | Direction section | *"Trois options : Soit on veut, soit non, soit on sait pas"* |
| `correlation.md` | Item 13 (line 77–79) | Confirmed: three outcomes — yes / no / need more info |
| `nikita_notes_2` | Project Evaluation section | *"Projects submitted for review must be displayed in descending order of score"* |
| `summary.md` | Lines 43–47 | Direction module description |

### Entry conditions
- Proposer submits from `DraftState`.
- Proposer resubmits after revision from `SuspendedState`.

### Exit conditions (transitions out)

| Transition | Target state | Trigger |
|------------|-------------|---------|
| Direction approves | `ApprovedState` → `CollectingState` | Manual action by Direction / RH |
| Direction refuses | `RefusedState` | Manual action by Direction / RH |
| Direction requests revision | `SuspendedState` | Manual action by Direction / RH |

### Rules while in this state
- The proposer cannot edit the proposal while it is under review.
- Direction can write comments at any time (`thomas.md:50`, confirmed in `correlation.md` item 14).
- **Age-based colour coding applies:**
  - > 1 month without action → **orange** (`thomas.md:87–88`, `tiziano.md` Particularité section)
  - > 2 months → **red** (`thomas.md:89–90`, `tiziano.md` Particularité section)
  - > 3 months → auto-transition to `ArchivedState` (`thomas.md:91–93`, `tiziano.md` Particularité section, `correlation.md` item 21 line 117–119, `summary.md` lines 64–66)

---

## State 3 — SuspendedState

**Laravel class:** `App\States\Project\SuspendedState`
**Stage:** Proposition (returned to proposer)
**Visible to:** proposer (edit), Direction (read + comment)

### What it is
A proposal that Direction has sent back to its author for revision or additional
information. The proposer must update and resubmit before Direction can act again.

### Source references

| Source | Location | Quote / note |
|--------|----------|--------------|
| `thomas.md` | Line 50 | *"you can we ask for more details"* |
| `tiziano.md` | Direction section | *"Elle peut remettre le projet à la personne qui la créer"* |
| `tiziano.md` | Direction section | *"Système de review pour les projets en suspend"* |
| `tiziano.md` | Roles section | *"Seul les RH peuvent valider et refuser les projets. Ou suspendre."* (introduces suspend as a named third action) |
| `correlation.md` | Item 14 (line 83–85) | Confirmed: direction can send back / request review |
| `correlation.md` | Discrepancy C (line 169–173) | Flagged: "suspend" appears only in Tiziano's notes — worth modelling as a status |
| `question.md` | Suspension section | *"Si un projet est reconduit, vous voulez que quand le collaborateur reçoit le projet avec la review il : reremplisse un nouveau formulaire ou ait accès à un onglet de correction."* — **this question has not been answered yet** |
| `summary.md` | Lines 45–46 | *"The direction can write comments and send the project back to its author for review"* |

### Entry conditions
- Direction suspends a `SubmittedState` project and writes a mandatory clarification comment.

### Exit conditions (transitions out)

| Transition | Target state | Trigger |
|------------|-------------|---------|
| Proposer edits and resubmits | `SubmittedState` | Manual action by proposer |

### Rules while in this state
- A direction comment explaining what needs changing is required before suspension.
- The proposer can edit all form fields.
- **Open question:** does the proposer edit the existing form or fill a new one? (`question.md`, Suspension section — unanswered).
- Age-based colour coding continues from the original submission date.

---

## State 4 — RefusedState  *(le frigo)*

**Laravel class:** `App\States\Project\RefusedState`
**Stage:** Direction archive ("le frigo")
**Visible to:** Direction, Admin (read-only)

### What it is
A proposal that Direction has permanently refused. It goes into the Direction
archive, nicknamed "le frigo" by the client. It is separate from the other stage
archives.

### Source references

| Source | Location | Quote / note |
|--------|----------|--------------|
| `thomas.md` | Line 52–54 | *"if the project is refused -> we put it in a back archivéé so a list of all the stuff archivéé / they call it the frigo"* |
| `tiziano.md` | After Direction section | *"Projets refusés : Archivés … Ils appellent ça le frigo"* |
| `correlation.md` | Item 15 (line 88–90) | Confirmed by both — the client's own slang term "le frigo" |
| `schema.md` | User flow diagram | `C -- Non --> E[On l'archive dans le frigo]` |
| `nikita.md` | Stage 2 | *"Reject the project. In this case, the project is automatically moved to the archive."* |
| `summary.md` | Lines 44–46 | *"Refused projects are archived in a list the client calls 'le frigo'"* |
| `question.md` | Frigos et archives section | *"Est-ce qu'on conserve les propositions de projet telles quelles dans le Frigo, avec description etc. ?"* — **unanswered** |
| `question.md` | Frigos et archives section | *"Qui a accès aux Frigos et aux archives ?"* — **unanswered** |

### Entry conditions
- Direction refuses a `SubmittedState` project.

### Exit conditions (transitions out)

| Transition | Target state | Trigger |
|------------|-------------|---------|
| Direction / Admin restores | `SubmittedState` | Manual action |

### Rules while in this state
- Project and all its form data are preserved read-only.
- The direction's refusal comment is preserved.
- Restorable to `SubmittedState` within the 1-year archive window (`nikita_notes_2`: *"Projects can remain in the archive for no more than one year"*).
- **Open question:** who exactly can access le frigo? (`question.md` — unanswered).
- **Open question:** are full form fields preserved or just a summary? (`question.md` — unanswered).

---

## State 5 — ApprovedState

**Laravel class:** `App\States\Project\ApprovedState`
**Stage:** Transition only (no UI view)
**Visible to:** Direction, Chef de projet

### What it is
A transient state that exists only to record that Direction approved the project.
The system immediately moves the project to `CollectingState` upon approval. In
practice, this state may be collapsed into `CollectingState` unless an audit
trail of the approval moment is needed (see `PROJECT_STATUS_HISTORY` in
`mcd.mermaid`).

### Source references

| Source | Location | Quote / note |
|--------|----------|--------------|
| `thomas.md` | Line 55–57 | *"if a project is accepted -> which passes inside the recolte"* |
| `tiziano.md` | After Direction section | *"si le projet est accepté : Il est dans la récolte"* |
| `correlation.md` | Item 16 (line 93–96) | Confirmed: accepted → récolte |
| `schema.md` | User flow diagram | `C -- Oui --> D[On l'envoie en récolte]` |
| `nikita.md` | Line 109 | `"→ Approved →"` in the recommended status chain |

### Entry conditions
- Direction approves a `SubmittedState` project.

### Exit conditions (transitions out)

| Transition | Target state | Trigger |
|------------|-------------|---------|
| System auto-advances | `CollectingState` | Immediately on approval |

---

## State 6 — CollectingState

**Laravel class:** `App\States\Project\CollectingState`
**Stage:** Récolte
**Visible to:** Récolte Manager, Chef de projet, Direction (read-only)

### What it is
An approved project that is actively gathering the resources and funding needed
to start. The récolte manager tracks resources found per phase. The project stays
here until either 80% of resources are found (→ `ReadyState`) or 12 months pass
(→ `ArchivedState`).

### Source references

| Source | Location | Quote / note |
|--------|----------|--------------|
| `thomas.md` | Line 58–60 | *"list of project -> for each project it has the list of etapes -> which has a case of the amount of money -> and the amount of resources need"* |
| `thomas.md` | Line 59 | *"they need add another line and signal they found the money -> it displayed 'okay we found 20% of the budget found yet'"* |
| `tiziano.md` | Récolte section | *"Elle a une liste de projet / Pour chaque projet / Elle a la liste d'étape / Elle a une case pour chaque informations / Elle va noter si elle trouve des infos / Avec un taux du budget indicatif"* |
| `correlation.md` | Item 17 (line 98–101) | Confirmed: récolte managed by different person, with steps and info fields per step |
| `thomas.md` | Line 64 | *"we need to filter -> ordre de proposition -> price -> filter by how much of the budget they found"* |
| `nikita.md` | Stage 3 (Approved Projects) | *"If funding is not available, the project is moved to the Funding Search category"* |
| `nikita_notes_2` | Financial Officer / Support section | *"Present only for projects that are approved and seeking funding"* || `nikita_notes_2` | Financial Officer / Support section | *"Present only for projects that are approved and seeking funding"* |

| `summary.md` | Lines 49–53 | Récolte module description |

### Entry conditions
- System auto-advances from `ApprovedState`.

### Exit conditions (transitions out)

| Transition | Target state | Trigger |
|------------|-------------|---------|
| `SUM(amount_found) / SUM(amount_needed) >= 0.80` | `ReadyState` | Automatic, triggered on each resource update |
| 12 months elapsed since entry | `ArchivedState` | Automatic (scheduled job) |

### Rules while in this state
- Resources are tracked at the `Phase_Resource` level: each row has `amount_needed` and `amount_found`.
- Percentage = `SUM(amount_found) / SUM(amount_needed)` across all phases of the project.
- List is filterable/sortable by percentage of resources found (`thomas.md:64`).
- **Age-based colour coding applies** (same as all other stages):
  - > 1 month → **orange** (`thomas.md:87–96`, `tiziano.md` Particularité section, `summary.md:64–66`)
  - > 2 months → **red**
  - > 3 months → would normally auto-archive, **but Récolte has a longer horizon**
- **Récolte-specific archive threshold:** 12 months (not 3) (`thomas.md:98–100`, `tiziano.md`: *"Recolte : Après 1 année, on archive auto"*, `correlation.md` item 22 line 122–124, `summary.md:69–71`)
- **Possible 6-month warning** (Thomas only, not corroborated): `thomas.md:98–100` — *"in recolte -> 6 months is the limit … less than 20% of the resources after 6 months"*. Tiziano does not mention this. **Treat as unconfirmed** until next client contact.

---

## State 7 — ReadyState

**Laravel class:** `App\States\Project\ReadyState`
**Stage:** Récolte (green)
**Visible to:** Récolte Manager, Chef de projet, Direction (read-only)

### What it is
A project in Récolte that has reached or exceeded 80% of its required resources.
It is displayed in green and can be launched into En cours. It does not
auto-advance — a human must confirm the launch.

### Source references

| Source | Location | Quote / note |
|--------|----------|--------------|
| `thomas.md` | Line 66–68 | *"if for one project we found 80% of the resources -> they can start the project / if we have less than 80% -> then the project stays in recolte"* |
| `tiziano.md` | Récolte section | *"Si on a trouvé 80% des ressources : projet passe en vert, alors là on peut commencer le projet"* |
| `correlation.md` | Item 18 (line 103–106) | Confirmed by both — 80% threshold, turns green |
| `summary.md` | Lines 51–53 | *"When ≥ 80% of resources are found, the project turns green and can start (move to 'En cours'). Below 80%, it stays in Récolte."* |

### Entry conditions
- Automatic: resource percentage reaches ≥ 80% while in `CollectingState`.

### Exit conditions (transitions out)

| Transition | Target state | Trigger |
|------------|-------------|---------|
| Récolte manager or chef de projet launches | `ActiveState` | Manual action |
| (Fallback) 12 months elapsed | `ArchivedState` | Automatic — same horizon as CollectingState |

### Rules while in this state
- Displayed in **green** in the Récolte list.
- Resource values remain editable (more funding can still arrive).
- If resources drop back below 80% (e.g. a sponsor withdraws), state reverts to `CollectingState`.

---

## State 8 — ActiveState

**Laravel class:** `App\States\Project\ActiveState`
**Stage:** En cours
**Visible to:** All authenticated users (read); Chef de projet + collaborateurs (comment)

### What it is
A project that is actively being worked on. The primary record-keeping mechanism
is timestamped comments from the chef de projet. Inactivity triggers email
escalation then auto-archiving.

### Source references

| Source | Location | Quote / note |
|--------|----------|--------------|
| `thomas.md` | Line 70–86 | En cours full description: name, description, budget, who works, resources, comment structure |
| `thomas.md` | Line 79–80 | *"the person responsible for the project -> they can make comments"* |
| `thomas.md` | Line 83–86 | Comment structure: *"person name / date / comment"* |
| `tiziano.md` | En cours section | *"Qui travaille, les ressources trouvées, description titre du projet / Commentaire de l'avancement du projet / Sorte d'analyse historique avec un timestamp"* |
| `tiziano.md` | En cours section | *"Idéalement seul le chef mais eventuellement les collaborateurs"* |
| `correlation.md` | Item 19 (line 109–111) | Confirmed: en cours shows who/resources/description + comments with timestamp |
| `correlation.md` | Item 20 (line 113–115) | Confirmed: comments mainly by chef, possibly collaborateurs |
| `nikita.md` | Progress Updates section | *"The project owner or project lead must provide a status update at least once per month"* |
| `summary.md` | Lines 55–61 | En cours module description |

### Entry conditions
- Récolte manager or chef de projet manually launches from `ReadyState`.

### Exit conditions (transitions out)

| Transition | Target state | Trigger |
|------------|-------------|---------|
| Chef de projet marks complete | `CompletedState` | Manual action |
| 3 months without any comment | `ArchivedState` | Automatic (scheduled job) |

### Rules while in this state
- **A comment resets the inactivity clock.** Last comment date drives all email and archiving logic.
- **Email escalation** (both sources agree):
  - 1 month no comment → reminder email to chef de projet. `last_reminder_at` is set.
    (`thomas.md:102–104`, `tiziano.md` En cours section: *"1 mois -> On reçoit un mail"*, `correlation.md` item 23 line 127–129)
  - 1 week after first email, still no comment → second stronger email CC'd to everyone with a follow-up role on the project.
    (`thomas.md:104–105`, `tiziano.md`: *"Si 1 semaine après 1er mail -> Reçois 2 mail (Mail plus solide, avec toutes les personnes qui ont le rôle de suivi)"*, `correlation.md` item 23, `summary.md:73–77`)
- **Age-based colour coding** (`thomas.md:87–96`, same rule as all stages):
  - > 1 month no update → **orange**
  - > 2 months no update → **red**
  - > 3 months no update → **auto-archive**
- **Open question:** what happens to a project that is abandoned mid-way (not completed, not stale enough to auto-archive)? (`question.md`: *"Ou va un projet jamais abouti (abandonné) en cours de route"* — **unanswered**).
- **Open question:** what happens to a project that needs re-evaluation mid-run (resources run out)? (`question.md`: *"Si un projet doit être réevalué (manque de ressource au moment de sa réalisation), qu'est-ce qu'on en fait ?"* — **unanswered**).

---

## State 9 — CompletedState

**Laravel class:** `App\States\Project\CompletedState`
**Stage:** En cours archive (completed)
**Visible to:** All authenticated users (read-only)

### What it is
A project that has been intentionally closed out by the chef de projet. It is
stored in the En cours archive and is permanently read-only.

### Source references

| Source | Location | Quote / note |
|--------|----------|--------------|
| `nikita.md` | Line 109 | `"→ Completed →"` in the recommended status chain |
| `question.md` | Projets inachevés section | *"Que deviennent les projets en cours qui ont été achevés ?"* — **this specific question was asked but not answered in the notes** |
| `question-client.md` | En cours section | *"Que deviennent les projets en cours qui ont dût être abandonnés / reconduits / achevés"* — **unanswered** |

### Entry conditions
- Chef de projet manually marks an `ActiveState` project as complete.

### Exit conditions
- None. `CompletedState` is a terminal state.
- Projects completed more than 1 year ago may be permanently deleted (`nikita_notes_2`: *"Projects can remain in the archive for no more than one year"*) — but the deletion policy for completed projects was not confirmed with the client.

### Rules while in this state
- All fields and comments are read-only.
- A final comment should be required before closing (not explicitly stated in notes, but implied by the comment-as-activity-log pattern).

---

## State 10 — ArchivedState

**Laravel class:** `App\States\Project\ArchivedState`
**Stage:** Per-stage archive (four separate archive views)
**Visible to:** Direction, Admin (confirmed); others unclear — **open question**

### What it is
A project that was automatically archived due to inactivity or age. There are
**four separate archives**, one per stage. Projects can be restored within the
1-year archive window.

### Source references

| Source | Location | Quote / note |
|--------|----------|--------------|
| `thomas.md` | Line 91–93 | *"3+ months / automatically archived"* |
| `thomas.md` | Line 119–121 | *"each section has their own archive section / if we want to restore, an archive they can in the right category"* |
| `tiziano.md` | Particularité section | *"Proposition de + 3 mois -> Archivées"* |
| `tiziano.md` | Récolte section | *"Recolte : Après 1 année, on archive auto"* |
| `correlation.md` | Item 21 (line 117–119) | Confirmed: 3-month auto-archive, per-pile |
| `correlation.md` | Item 26 (line 141–143) | Confirmed: separate archives per section, restorable to correct category |
| `nikita.md` | Archiving section | Per-stage archives described with the same colour/timing rules |
| `nikita_notes_2` | Archive Limits section | *"Projects can remain in the archive for no more than one year"* |
| `summary.md` | Lines 64–79 | Cross-cutting rules: colour coding, récolte horizon, archives |
| `question.md` | Frigos et archives section | *"Qui a accès aux Frigos et aux archives ?"* — **unanswered** |
| `question.md` | Délais de suppression section | *"Comment gérez vous les vieilles archives, combien de temps vous les garder"* — **unanswered** |

### The four archive buckets

| Archive | Stage it belongs to | Auto-archive trigger |
|---------|--------------------|-----------------------|
| Proposition archive | Proposition | 3 months in `SubmittedState` / `SuspendedState` without activity |
| Le frigo | Direction | Manual refusal by Direction |
| Récolte archive | Récolte | 12 months in `CollectingState` / `ReadyState` |
| En cours archive | En cours | 3 months in `ActiveState` without a comment, or manual completion |

### Entry conditions

| From state | Trigger |
|-----------|---------|
| `SubmittedState` | 3 months without a direction action |
| `SuspendedState` | 3 months without a proposer revision |
| `RefusedState` | Manual — direction refuses (goes to le frigo) |
| `CollectingState` | 12 months elapsed |
| `ReadyState` | 12 months elapsed (same horizon as Récolte) |
| `ActiveState` | 3 months without a comment |
| `CompletedState` | Manual or 1-year archive limit |

### Exit conditions (restoration)

| Transition | Target state | Trigger |
|------------|-------------|---------|
| Restore from Proposition archive | `SubmittedState` | Manual, Direction / Admin |
| Restore from le frigo | `SubmittedState` | Manual, Direction / Admin |
| Restore from Récolte archive | `CollectingState` | Manual, Récolte Manager / Admin |
| Restore from En cours archive | `ActiveState` | Manual, Chef de projet / Admin |

### Rules while in this state
- `archived_at` column is set; `restored_at` is cleared.
- Restoration sets `restored_at` and clears `archived_at`.
- Inactivity clock resets on restoration.
- **1-year hard limit:** projects not restored within 1 year of archiving are permanently locked (`nikita_notes_2` Archive Limits section). Permanent deletion policy not confirmed.
- **Open question:** who exactly can access which archives (`question.md` — unanswered).

---

## Transition Table (complete)

| From | To | Actor | Condition |
|------|----|-------|-----------|
| `DraftState` | `SubmittedState` | Proposer | Form is complete and valid |
| `SubmittedState` | `SuspendedState` | Direction | Direction writes clarification comment |
| `SubmittedState` | `RefusedState` | Direction | Direction refuses with mandatory comment |
| `SubmittedState` | `ApprovedState` | Direction | Direction approves |
| `SubmittedState` | `ArchivedState` | System | 3 months without direction action |
| `SuspendedState` | `SubmittedState` | Proposer | Proposer edits and resubmits |
| `SuspendedState` | `ArchivedState` | System | 3 months without proposer revision |
| `ApprovedState` | `CollectingState` | System | Auto on approval |
| `RefusedState` | `SubmittedState` | Direction / Admin | Manual restoration from le frigo |
| `CollectingState` | `ReadyState` | System | `amount_found / amount_needed >= 0.80` |
| `CollectingState` | `ArchivedState` | System | 12 months elapsed |
| `ReadyState` | `ActiveState` | Récolte Manager / Chef | Manual launch |
| `ReadyState` | `CollectingState` | System | Resources drop back below 80% |
| `ReadyState` | `ArchivedState` | System | 12 months elapsed |
| `ActiveState` | `CompletedState` | Chef de projet | Manual close |
| `ActiveState` | `ArchivedState` | System | 3 months without a comment |
| `ArchivedState` | `SubmittedState` | Direction / Admin | Restore from Proposition archive or le frigo |
| `ArchivedState` | `CollectingState` | Récolte Mgr / Admin | Restore from Récolte archive |
| `ArchivedState` | `ActiveState` | Chef / Admin | Restore from En cours archive |

---

## Timing Rules Summary

| Stage | Orange | Red | Auto-archive | Source |
|-------|--------|-----|-------------|--------|
| Proposition | + 1 month | + 2 months | + 3 months | `thomas.md:87–96`, `tiziano.md` Particularité, `correlation.md` item 21 |
| Direction | + 1 month | + 2 months | + 3 months | same |
| Récolte | + 1 month | + 2 months | **+ 12 months** | `thomas.md:98–100`, `tiziano.md` Récolte, `correlation.md` item 22 |
| En cours | + 1 month | + 2 months | + 3 months | same |

### En cours email escalation

| Delay | Action | Source |
|-------|--------|--------|
| 1 month no comment | Email #1 to chef de projet | `thomas.md:102–104`, `tiziano.md` En cours, `correlation.md` item 23 |
| +1 week, still no comment | Email #2 (stronger), CC all follow-up roles | `thomas.md:104–105`, `tiziano.md` En cours, `correlation.md` item 23 |
| 3 months no comment | Auto-archive | `thomas.md:91–93`, `summary.md:64–66` |

---

## Open Questions About States

These questions are documented in `question.md` and `question-client.md` and
have **not been answered** in any of the interview notes.

| Question | File | Section | Impact |
|----------|------|---------|--------|
| Does the proposer fill a new form on revision or edit the existing one? | `question.md` | Suspension de projet | Affects whether `SuspendedState` triggers a new `Projects` row or edits the existing one |
| What happens to an abandoned in-progress project? | `question.md` | Projets inachevés | Missing state — needs a `AbandonedState` or a flag on `ArchivedState` |
| What happens to a project that needs re-evaluation mid-run? | `question.md` | Projets inachevés | Could need a `ReEvaluationState` or a return to `CollectingState` |
| What happens to completed projects after the 1-year archive window? | `question.md` / `question-client.md` | Projets en cours | Hard-delete, soft-delete, or permanent lock? |
| Who can access le frigo and the stage archives? | `question.md` | Frigos et archives | Affects visibility gates on all archive views |
| Are proposals preserved in full in le frigo (all form fields)? | `question.md` | Frigos et archives | Affects whether to soft-delete or snapshot the `Projects` row |
| How long are archives kept before permanent deletion? | `question.md` | Délais de suppression | Archive retention policy — `nikita_notes_2` says 1 year but client hasn't confirmed |
| Can a direction member propose and self-validate? | `question.md` | Direction — proposition | Affects whether a self-validation guard is needed |
| Is the 6-month / <20% warning in Récolte real? | `correlation.md:122–125` + `thomas.md:98–100` | Discrepancy D | Only in Thomas's notes — not confirmed by Tiziano |
