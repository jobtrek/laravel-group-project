# Issue #202 — Manual Phase Transition & Resource Contribution Form

## 1. Manual Transition Logic (Récolte → En Cours)

Update the 80% logic so the transition from **récolte** to **en cours** is no longer automatic — it should require a manual action via a button.

**Files likely involved:**

- `app/Service/ProjectService.php`
- `app/Http/Controllers/RecolteController.php`

## 2. Update `resource-contribution-form.blade.php`

- Remove the **phase selection dropdown**.
- Replace it with a **first dropdown** populated with users from the DB.
    - Before creating a new method for this, verify whether one already exists.
    - Before creating a new component check if there is an existing one that can be reused.
    - If you add js logic, ensure it is **reusable** and **not hardcoded** to this specific form.
    - and it's not within the same .blade component/page


## 3. Chief of Project Selection Logic

- When a **chief of project** is selected, show a **second dropdown** (for adding members).
    - Include a **`+` button**, similar to the pattern in `resources/views/components/proposition/wizard.blade.php`.
    - Use similar logic to the chief-of-project dropdown.
- Once a chief of project is selected, the project can be **manually transitioned to "en cours"** — **members are not required** for this transition.

## Notes

- Please read **issue #202** carefully via `gh` before starting.
- If you run into issues, load the **Laravel systematic debugging** skill.
- While implementing, load the **Laravel best practices** skill.
