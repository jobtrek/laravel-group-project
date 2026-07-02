# Issue #202 — Final Check & Resource Contribution Form UI Fixes

## 1. Resource Type Selection — Dropdown Instead of Free Text

**Current behavior:** Users can freely type the "type" of resource in the contribution form.

**Required behavior:**

- Replace the free-text input with a **dropdown**.
- Populate the dropdown with the same resource types defined in `resources/views/components/proposition/wizard.blade.php` (the dropdown already used in the proposition form).
- The available types should match what was originally selected/defined during the **proposition** phase for that project — i.e. only the resource types relevant to this specific project should appear, not a global static list.
- Once a type is selected, the user should be able to input the **amount** needed/contributed for that type.

**Technical notes:**

- Check whether resource types are stored as an enum, a lookup table, or hardcoded in the wizard component — this determines whether the dropdown can share a single source of truth (e.g. a shared Blade component, a config array, or a model-backed list) or needs duplication.
- If types are project-specific (defined at proposition time), the dropdown options will need to be scoped by `project_id` rather than global.

## 2. Project Details Page — "En Cours" State Display

**File:** `resources/views/projectsDetails.blade.php`

When viewing a project's details while it is in the **en cours** state, the page should display:

- **Chef de projet** (chief of project)
- **Members** of the project
- **Progress bar**, updated to reflect:
    - Amount of resources **contributed** vs.
    - Amount of resources **needed**, per resource type

**Technical notes:**

- Confirm whether progress should be a single aggregate bar (total contributed / total needed across all types) or broken down per resource type (multiple bars/sections).
- This ties into the earlier ask (Issue #202, point 3) that resource-type contributions should also reflect in the `phase_details.blade` resource-type section — confirm if `projectsDetails.blade.php` and `phase_details.blade.php` should share the same progress-calculation logic/component.

## Context & Process

- Before implementing, read the **specs files** — specifically anything beyond `formulaire_spec` — for full context on the intended design.
- If any information across specs or prior requests is **contradictory or unclear**, use the **grilling technique** (ask clarifying questions) before implementing. Do not guess — get the answer first, then build the feature according to that answer.
- **Once the feature is implemented, update the spec file** with a summary of the solution, so the next agent has context on what was done.
