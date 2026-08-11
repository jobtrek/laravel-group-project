# Issue #347: Duplicated phase/resource data-shaping logic

## The issue
`edit.blade.php`, `revision-form.blade.php`, and `wizard.blade.php`/`step-2.blade.php`
each independently reimplemented the same phases/objectifs/livrables/ressources
data-shaping — both server-side (`@php` mapping `Project`/`old()` input into a
nested array) and client-side (matching Alpine `x-data` push/remove handlers).

## Changes
- `app/Models/PhaseResource.php` — added `toFormArray()` → single place that
  maps a resource to `{id, resource_type, amount_needed}`.
- `app/Models/ProjectPhase.php` — added `toFormArray()` → single place that
  maps a phase (+ its resources) to the nested form array shape.
- `resources/views/edit.blade.php` — replaced its inline map closure with
  `$phase->toFormArray()`; replaced its addPhase/removePhase/addObjectif/
  removeObjectif/addLivrable/removeLivrable/addResource/removeResource
  methods with a spread of `window.phaseRepeaterFactory(...)`.
- `resources/views/revision-form.blade.php` — replaced its inline resource
  default-mapping with `$r->toFormArray()`; replaced its 3 hand-rolled
  id-keyed list `x-data` blocks (but/objectifs/livrables) with
  `window.listHelpers.toIdList()` + `<x-proposition.repeatable-list>`, and
  the resources block's id-wrapping with `window.listHelpers.toIdRows()`.
- `resources/views/components/proposition/wizard.blade.php` — phases initial
  value + add/removeObjectif/Livrable/Resource now come from
  `window.phaseRepeaterFactory(...)` (spread); `addPhase`/`removePhase` stay
  wizard-specific since they also sync `phaseErrors`. Removed the now-unused
  generic `removeItem()`. `buts` init now uses `window.listHelpers.toIdList()`.
- `resources/views/components/proposition/step-2.blade.php` — objectif/
  livrable/resource add & remove buttons now call the shared
  `addObjectif`/`removeObjectif`/`addLivrable`/`removeLivrable`/
  `addResource`/`removeResource` methods instead of inlining
  `push`/`removeItem` calls, so it actually uses the shared factory.
- `resources/views/components/proposition/repeatable-list.blade.php` —
  extended with `nameExpr` (dynamic `:name` binding) and `required` props so
  it can serve `revision-form.blade.php`'s per-field correction inputs, not
  just `step-1.blade.php`'s static `buts[]` field.
- `resources/js/app.js` — added `window.listHelpers.toIdList()` /
  `toIdRows()` (centralizes the `{id: crypto.randomUUID(), ...}` wrapping
  pattern that was duplicated 4x); added `window.phaseRepeaterFactory()`
  (the shared phases-repeater data/handlers, parameterized by
  `resourcesKey`, `withIds`, `defaultResources`, `minResources` to cover
  edit's and the wizard's small shape differences). `userMultiSelect` now
  reuses `toIdList()` internally.
- `tests/Feature/PhaseFormViewsRenderTest.php` — new regression test:
  renders `edit`, `revision-form`, and `create` (wizard) end-to-end via HTTP
  to catch Blade/JS wiring breakage from the refactor.

## How it fits together
- PHP: `ProjectPhase::toFormArray()` / `PhaseResource::toFormArray()` are the
  single source for "how does a persisted phase/resource look as form data" —
  used by both `edit.blade.php` (full form) and `revision-form.blade.php`
  (per-field resource defaults).
- JS: `window.phaseRepeaterFactory()` is the single source for the
  phases-repeater's shape and push/remove handlers — spread into both
  `edit.blade.php`'s and `wizard.blade.php`'s `x-data`, with small options
  covering the field-name (`ressources` vs `ressources_necessaires`) and
  persisted-id differences. `window.listHelpers.toIdList()`/`toIdRows()`
  cover the separate "wrap old()/DB values with a client-side id for x-for
  keys" pattern used by `userMultiSelect` and `revision-form.blade.php`'s
  correction fields.
- `<x-proposition.repeatable-list>` is the one shared markup partial for
  simple string-list repeaters (buts/objectifs/livrables), now used by both
  `step-1.blade.php` and `revision-form.blade.php`.

## Review notes
- Independent review pass found no correctness bugs.
- wizard.blade.php's `addPhase`/`removePhase` intentionally override the
  factory's versions (defined later in the same `x-data` object literal) to
  also sync `phaseErrors` — small, deliberate duplication kept out of the
  shared factory since it's wizard-specific validation bookkeeping.
- revision-form's resource list now seeds its client-side `:key` from the
  DB `id` (via `toFormArray()`) instead of always generating a fresh UUID;
  harmless since `id` is never part of the submitted `corrections[...]`
  payload for that field.
