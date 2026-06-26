# Proposition Form — Field Reference

Derived from the client PDF template ("Canevas de proposition de projet") and both
interview notes ([thomas.md](thomas.md), [tiziano.md](tiziano.md)).

---

## Header

| Field       | Type               | Notes                                          |
|-------------|--------------------|------------------------------------------------|
| `titre`     | text input         | Title of the project proposal                  |
| `porteur`   | text / user select | Name of the person guaranteeing the proposal   |
| `membres`   | multi-select / text | Names of all people involved                  |

---

## Section 1 — Description

| Field         | Type     | Notes                                                                 |
|---------------|----------|-----------------------------------------------------------------------|
| `description` | textarea | Short description of the project and the problem it solves. Max 3 paragraphs. |

---

## Section 2 — Buts

| Field  | Type         | Notes                                                                                   |
|--------|--------------|-----------------------------------------------------------------------------------------|
| `buts` | dynamic list | Main goals of the project. Should be SMART and aligned with the foundation's mission/vision. Repeatable items. |

---

## Section 3 — Périmètre *(optional)*

| Field      | Type     | Notes                                                                              |
|------------|----------|------------------------------------------------------------------------------------|
| `perimetre` | textarea | What the project will and will not do. Can be omitted for small/simple projects.  |

---

## Section 4 — Phases du projet *(repeatable block)*

The user can add as many phases as needed. Each phase is its own sub-form.

### Per-phase fields

| Field                   | Type         | Notes                                                           |
|-------------------------|--------------|-----------------------------------------------------------------|
| `titre`                 | text input   | Name of the phase                                               |
| `duree`                 | text input   | Expected duration — e.g. "3 mois", "1 semaine", "1 an"         |
| `description`           | textarea     | Overview of what this phase covers                              |
| `objectifs`             | dynamic list | Measurable objectives — what success looks like for this phase  |
| `livrables`             | dynamic list | Deliverables produced — documents, processes, events, etc.      |
| `ressources_necessaires`| dynamic list | Required resources by type and amount — see format below        |

### Ressources nécessaires — line item format

Each line is a free-text resource entry. Examples from the PDF:
- `1 personne à 20% durant toute la phase`
- `1000 CHF pour l'achat de matériel`

---

## Section 5 — Ressources totales *(only shown if the proposal has more than 2 phases)*

| Field                    | Type              | Notes                                               |
|--------------------------|-------------------|-----------------------------------------------------|
| `ressources_totales`     | textarea or auto  | Summary of all resources across all phases combined |

---

## Section 6 — Évaluation (Impact Matrix)

The foundation's own scoring matrix. All four scores are required.

| Field       | Type        | Range    | Description                                                                 |
|-------------|-------------|----------|-----------------------------------------------------------------------------|
| `portee`    | number      | 0 – 50   | People impacted: direct collaborators count 1pt, partners 0.5pt, max 50    |
| `impact`    | select 1–5  | 1–5      | See scale below                                                             |
| `confiance` | number      | 0–100 %  | Certainty the project will succeed/be completed                             |
| `effort`    | select 1–5  | 1–5      | See scale below                                                             |

### Impact scale

| Score | Meaning                                                                                          |
|-------|--------------------------------------------------------------------------------------------------|
| 1     | Invisible at the foundation level — negligible, concerns only a few roles                        |
| 2     | Measurable but limited effect — restricted to one product or circle, not necessarily lasting     |
| 3     | Creates a new "capacity" for Jobtrek — improvement touching several products or circles over time |
| 4     | Significantly changes how circles/products operate — durable, creates an important advantage     |
| 5     | Transformational — the foundation enters a new era, global and lasting change over a decade      |

### Effort scale

| Score | Meaning                                                                                                         |
|-------|-----------------------------------------------------------------------------------------------------------------|
| 1     | A few days of work (≤ 1 week ETP cumulated). Very few financial resources (< 200 CHF). Near-zero logistics.    |
| 2     | A few weeks (1–4 weeks ETP). Minor financial investment (< 2 000 CHF). Coordination within one autonomous team. |
| 3     | Several months part-time (1–3 months ETP). Moderate external cost (2 000–10 000 CHF). Requires inter-team sync. |
| 4     | More than a semester (3–6 months ETP). Significant external cost (10 000–50 000 CHF). Steering committee needed.|
| 5     | Several years at >100% ETP continuously. Heavy investment (> 50 000 CHF). Multi-phase validation required.      |

---

## Frontend notes

- **Phases** are the most complex UI element — use a dynamic "add phase / remove phase" pattern with the three inner lists (objectifs, livrables, ressources) each having their own "add row / remove row".
- **Section 5 (Ressources totales)** should only render when the phase count exceeds 2.
- The **Évaluation section** uses specific scale descriptions — show tooltips or an expandable helper so the user knows what each score means.
- **Buts, objectifs, livrables, ressources** are all lists — keep the UI consistent across all of them.
- This form feeds directly into the **Direction** queue (module 2) once submitted.
