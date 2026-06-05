# Project Summary — Confirmed Requirements

This summary lists **only what both interview notes agree on** — the requirements
we are sure of. Open questions and conflicts are intentionally left out (see
[correlation.md](correlation.md) for those).

---

## What the app is

- A **project-tracking / follow-up app** — it tracks the progress of projects.
- It is explicitly **NOT a management tool** ("application de suivi, pas de gestion").
- It is a **portfolio view**: it shows projects, who works on them, and the budget — but **not** a detailed per-project to-do list.
- The visual **design should be sober / minimal**.

## Who uses it

- A foundation with collaborators. Projects are proposed **internally**.
- **Anyone can propose a project** (distributed, autonomous hierarchy — proposals don't have to come from above).
- The **project lead is NOT the person who initiated the project** (two separate roles).
- A person's permissions are **scoped to their area**, except the project lead, who can see everything.

## The lifecycle (4 modules)

```
1. Proposition  ->  2. Direction  ->  3. Récolte  ->  4. En cours
   (anyone)            (decide)         (gather ≥80%)    (track)
        |                  |                 |               |
        +------------------+-----------------+---------------+--> Archives
                    refused archive = "le frigo"
```

### 1. Proposition
A person proposes a project containing:
- **Goal / objective**
- **Description** (incl. what problem it solves)
- **Budget**
- **Planning** — with a **work-rate (taux)** for each step/task
- **Impact analysis** — whether it's interesting for the foundation, including possible **negative impact**. The foundation has **its own impact matrix**.

### 2. Direction
- Lists all proposals and **sorts/filters them by impact**.
- Three possible outcomes: **yes / no / need more info**.
- The direction can **write comments** and **send the project back** to its author for review.
- **Refused projects are archived** in a list the client calls **"le frigo"** (the fridge).

### 3. Récolte (resource gathering)
- An **accepted** project moves here.
- Managed by a **different person** than the proposer.
- Shows a list of projects, each with its **list of steps** and an **info field per step** (amount of money / resources, with an indicative budget rate).
- This person notes resources/funds as they are found.
- When **≥ 80% of resources are found**, the project **turns green** and can start (move to "En cours"). Below 80%, it stays in Récolte.

### 4. En cours (in progress)
Each project shows:
- **Title / description**
- **Who works on it** and the **resources found**
- **Progress comments** with a **timestamp** (a historical log)
- Comment structure: **person name, date, comment**
- Comments are made **mainly by the project lead**, possibly by collaborators.

## Cross-cutting rules

### Age-based color coding (per pile)
- **+1 month** → **orange**
- **+2 months** → **red**
- **+3 months** → **auto-archived**

### Récolte archiving
- Récolte projects **auto-archive after ~12 months** (1 year).

### Email reminders for "En cours"
- After **1 month** with no update → a **reminder email** is sent.
- **1 week after** that first email with still no update → a **second, stronger email**, copied to **everyone with a follow-up role** on the project.


### Archives
- **Each stage has its own separate archive.**
- Archived projects can be **restored to the correct category**.

---

## One-line takeaway

A sober, internal **portfolio tracking app** where anyone can propose a project that
flows **Proposition → Direction → Récolte → En cours**, with refused items going to
**"le frigo"**, an **80% resource threshold** to start, **age-based color coding**,
**email escalation** for stalled projects, and **per-stage archives**.


## Roles
- Proposer: can propose projects, see their own projects, and see all projects in "En cours".
- Project lead: can see and comment on all projects in "En cours", regardless of whether they proposed them or not.
- Direction: can see and comment on all projects in "Direction", and can move projects between stages.
- Récolte manager: can see and comment on all projects in "Récolte", and can move projects between stages.



