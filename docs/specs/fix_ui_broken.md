# Issue #202 — Resource Contribution Form Fixes & Improvements

> Status: **Implemented**.

Okay so there is another problem with the UI, when a user select a chef de project, then clicks the button enregistrer, then the next time the user navigates to the page, the change should be reflected in the dropdown, prefilling it or disabling the input to the chef du project, the members can stay dynamic however still prefilled with the previous selection, and the button should be enabled since a chef de project is already selected.

check again also, regarding the state of if a chef du project is selected or not, if a chef du project is selected, the button should be enabled, if not, it should be disabled.

# technical details

when a project chief is selected, it should be saved to the database, and the next time the user navigates to the page, it should be reflected in the dropdown, prefilled or disabled. The members can stay dynamic but still prefilled with the previous selection. The button should be enabled since a chef de project is already selected.

## Resolution

Root cause: the previous refactor (`fix_permissions_refactor.md`) merged "Enregistrer l'équipe" and "Démarrer le projet" into a single request/button, on the assumption that saving a chief and starting the project were always the same action. That's wrong — a recolte manager can legitimately pick a chief, save, and keep working (adding resources/members) over multiple visits before actually starting the project. Under the merge, there was no "Enregistrer" button left to click independently, so "save chief, come back later, see it prefilled" was structurally impossible: a successful save always immediately transitioned the project out of Récolte.

Grilled one question before implementing: **should the chief `<select>` become disabled/locked once a chief is saved, or stay editable with the current chief pre-filled?** No response came back in time, so proceeded with the recommended default — **editable, pre-filled**. Reasoning: matches how the members list already behaves (dynamic/editable), and a genuinely disabled `<select>` doesn't submit its value on native form POST, which would require an extra hidden-input workaround for no real benefit — the manager should be able to correct a wrong pick before the project starts. Revisit if a hard lock turns out to be wanted after all.

Fix: split back into two independent actions:
- **`PATCH projects.recolte.team`** (`RecolteController::assignTeam`, button text "Enregistrer") — validated by `AssignProjectTeamRequest` (`leader_id` required), persists `leader_id` + `membres`, does **not** transition. Redirects back with a success message.
- **`PATCH projects.recolte.activate`** (`RecolteController::moveFromRecolteToActive`, button text "Démarrer le projet") — takes no body, just re-checks `ProjectService::moveToEncours($project)` against whatever is already persisted, and transitions if a chief is present.

Both dropdowns default from the project's current DB state, falling back to blank/empty only when unset:
- Chief: `x-data="{ chiefId: @js(old('leader_id', (string) $project->leader_id)) }"` on the page container (still used by the Démarrer button's `:disabled` binding).
- Members: `x-user-select-list :selected="old('membres', $project->members->pluck('id')->map(fn ($id) => (string) $id)->all() ?: [''])"`.

The chief `<select>` still uses `<option value="" selected disabled hidden>` as the placeholder — since it's disabled, once any real chief is prefilled or picked, the user cannot navigate the UI back to the empty placeholder (only backend validation on the *save* action, and the fact that `démarrer` no longer accepts any input, guard against that path — closing the original `fix_permissions_refactor.md` §1 bug from a different angle now that these are separate actions again).

The Démarrer button lives in its own `<form>` (no fields, no `form=` cross-reference needed anymore since it doesn't submit chief/members) positioned after both the team and resource forms, `:disabled="!chiefId"` reactive to the same page-level Alpine state — enabled immediately if a chief was already saved, disabled if not, live-updating as the user picks one in the team form (even before clicking Enregistrer, matching "if a chef du project is selected, the button should be enabled, if not, it should be disabled").

### Verification performed

- Rewrote `tests/Feature/ProjectTransitions/MoveFromRecolteToActiveTest.php` for the now-bodyless activate action (refuses without a saved chief, transitions once one exists).
- Added `tests/Feature/ProjectTransitions/AssignTeamTest.php` for the restored save-only action (rejects missing chief without a DB write; saves chief+members without transitioning).
- Full test suite, `phpstan analyse`, and `pint --dirty` all pass (same pre-existing unrelated failures as before: missing pgsql driver / `UserFactory::unverified()`).
- Per instruction, did **not** use `agent-browser` this round. Instead rendered `resource-contribution-form.blade.php` directly via `view(...)->render()` in Tinker against both a project with a pre-saved chief/member (confirmed both names appear pre-selected in the rendered HTML) and a project with none (confirmed the placeholder stays `disabled hidden` and `chiefId` initializes to `''`).
