# Issue #322: project_status.blade.php — state-to-label mapping duplicated from State classes

## What changed
- `app/Models/States/ProjectState.php`: added `abstract public function color(): string;` alongside the existing `label()` contract, so every state must declare its own display label and badge style.
- `app/Models/States/PropositionState.php`: `label()` now returns the human-readable `'Proposition'` (was the lowercase internal name); added `color()` returning the cyan/indigo badge classes.
- `app/Models/States/EvaluationState.php`: `label()` returns `'Évaluation'`; added `color()` (cyan/indigo).
- `app/Models/States/RevisionState.php`: `label()` returns `'Révision'`; added `color()` (orange).
- `app/Models/States/RecolteState.php`: `label()` returns `'Récolte'`; added `color()` (yellow).
- `app/Models/States/EncoursState.php`: `label()` returns `'En cours'`; added `color()` (yellow).
- `app/Models/States/CompleteState.php`: `label()` returns `'Complété'`; added `color()` (green).
- `app/Models/States/ArchiveState.php`: `label()` returns `'Archivé'`; added `color()` (gray).
- `resources/views/components/project_status.blade.php`: removed the hard-coded `$styles` and `$labels` arrays; the component now calls `$status->label()` and `$status->color()` directly on the `ProjectState` instance passed in, falling back to a neutral gray badge if a raw string is passed instead of a state object.

## Why
The badge markup previously kept its own copy of every state's display name (`$labels`) and Tailwind classes (`$styles`), independent of the state classes in `app/Models/States/`. Adding a new project state required editing three places — the state class, and both arrays in the blade file — and forgetting one of the arrays silently showed the raw internal state string instead of a label.

`ProjectState` already defined an abstract `label()` per subclass, but its implementations returned the lowercase internal `$name` value rather than a human-readable string, so it wasn't actually usable as a display label yet. Nothing else in the codebase depended on the previous lowercase return value (`label()` is not called anywhere for `ProjectState` outside this component), so it was safe to change its meaning to "human-readable label" and add a matching `color()` method.

This keeps all state-derived presentation data (value, label, color, editability, transitions) colocated on the state class itself — adding a new `ProjectState` subclass now only requires implementing `label()` and `color()` on that one class; the blade component needs no further changes.
