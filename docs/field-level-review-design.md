# Field-Level Project Review / Revision — Design & Feasibility

**Status:** Design pass — no implementation code yet.

---

## 1. Relevant Schema (current state)

### `projects`
| Column | Type | Notes |
|---|---|---|
| `id` | bigint PK | |
| `title` | varchar(100) | |
| `description` | text | |
| `budget_global` | decimal(10,2) nullable | |
| `but` | json | cast to array |
| `perimetre` | text nullable | |
| `ressources_totales` | text nullable | added in second migration |
| `status` | varchar(50) | cast to `ProjectState` via Spatie |
| `current_stage` | varchar(50) | currently set to same value as status; not cast |
| `proposer_id` | FK → users | |
| `leader_id` | FK → users nullable | |
| `recolte_manager_id` | FK → users nullable | |
| `archived_at`, `restored_at`, `last_reminder_at` | timestamps nullable | |

### `project_phases`
| Column | Type | Notes |
|---|---|---|
| `id` | bigint PK | |
| `name` | text | |
| `description` | text nullable | |
| `duration` | varchar(100) nullable | |
| `objectifs` | json nullable | array of strings |
| `livrables` | json nullable | array of strings |
| `order` | integer default 0 | display order only, not identity |
| `project_id` | FK → projects cascade | |

### `phase_resources`
| Column | Type | Notes |
|---|---|---|
| `id` | bigint PK | |
| `resource_type` | varchar(100) | |
| `description` | text nullable | |
| `work_rate` | integer nullable | |
| `amount_needed` | decimal(10,2) | |
| `amount_found` | decimal(10,2) default 0 | |
| `phase_id` | FK → project_phases cascade | |

### `project_evaluations`
| Column | Type | Notes |
|---|---|---|
| `id` | bigint PK | |
| `portee` | decimal(10,2) | 0–50 |
| `impact` | integer | 1–5 |
| `confiance` | integer | 0–100 |
| `effort` | integer | 1–5 |
| `project_id` | FK → projects cascade | |

### `project_reviews`
| Column | Type | Notes |
|---|---|---|
| `id` | bigint PK | |
| `project_id` | FK → projects cascade | |
| `user_id` | FK → users cascade | the director who submitted this review |
| `review_status` | varchar(50) | **currently unused** — no controller or service writes to it |
| `created_at`, `updated_at` | timestamps | |

### `comments`
| Column | Type | Notes |
|---|---|---|
| `id` | bigint PK | |
| `content` | text | |
| `stage` | varchar(50) | e.g. `'modification'`, `'direction'` |
| `user_id` | FK → users cascade | |
| `project_id` | FK → projects cascade | |
| `created_at`, `updated_at` | timestamps | |

**Current comment usages (full list):**
- `Project::comments()` — `HasMany` relationship, no default scopes
- `User::comments()` — `HasMany` relationship
- `testDirectionFront.blade.php:70` — `$project->comments->where('stage', 'modification')` (in-memory collection filter)
- **No other files reference the `comments` table.** No unique constraints. No indexes beyond the implicit PK.

---

## 2. Can We Extend `comments`?

**Yes — extending is workable and the right call.**

Adding four columns is fully backward-compatible:

| New column | Type | Default | Effect on existing rows |
|---|---|---|---|
| `field_key` | varchar(100) nullable | NULL | existing rows → NULL (general comments, not field-specific) |
| `requires_revision` | boolean | false | existing rows → false (correct — they're not revision requests) |
| `resolved_at` | timestamp nullable | NULL | existing rows → NULL (not applicable) |
| `review_id` | FK → `project_reviews` nullable, set null on delete | NULL | existing rows → NULL |

The only existing query — `$project->comments->where('stage', 'modification')` — is an in-memory filter on an already-loaded collection. It will continue to work unchanged and will include both field-level revision comments and any free-text modification comments, which is the right behavior (it shows the director's feedback alongside any general comment).

**The `Comment` model's `$fillable` array** needs `field_key`, `requires_revision`, `resolved_at`, and `review_id` added when implementation starts, but this is not a schema conflict.

---

## 3. Proposed Migration

One migration adding the four columns to `comments` and populating `review_status` values on `project_reviews`:

```php
// database/migrations/YYYY_MM_DD_HHMMSS_extend_comments_for_field_review.php

Schema::table('comments', function (Blueprint $table) {
    $table->string('field_key', 100)->nullable()->after('stage');
    $table->boolean('requires_revision')->default(false)->after('field_key');
    $table->timestamp('resolved_at')->nullable()->after('requires_revision');
    $table->foreignId('review_id')
        ->nullable()
        ->after('resolved_at')
        ->constrained('project_reviews')
        ->onDelete('set null');

    $table->index(['project_id', 'requires_revision', 'resolved_at'],
        'comments_revision_lookup');
});
```

The index on `(project_id, requires_revision, resolved_at)` covers the revision page query: `WHERE project_id = ? AND requires_revision = true AND resolved_at IS NULL`.

No changes to `project_reviews` schema are needed — `review_status` is already a varchar(50) and the new values (`approved`, `denied`, `revision`) fit.

---

## 4. Field Addressing Scheme

### Key format

Use dot-separated strings stored in `comments.field_key`:

| What is flagged | `field_key` value | Resolves to |
|---|---|---|
| Project title | `title` | `projects.title` |
| Project description | `description` | `projects.description` |
| Goals | `but` | `projects.but` (json array) |
| Scope | `perimetre` | `projects.perimetre` |
| Total resources | `ressources_totales` | `projects.ressources_totales` |
| Evaluation — portée | `evaluation.portee` | `project_evaluations.portee` |
| Evaluation — impact | `evaluation.impact` | `project_evaluations.impact` |
| Evaluation — confiance | `evaluation.confiance` | `project_evaluations.confiance` |
| Evaluation — effort | `evaluation.effort` | `project_evaluations.effort` |
| Phase name | `phases.{phase_id}.name` | `project_phases(id={phase_id}).name` |
| Phase duration | `phases.{phase_id}.duration` | `project_phases(id={phase_id}).duration` |
| Phase description | `phases.{phase_id}.description` | `project_phases(id={phase_id}).description` |
| Phase objectives | `phases.{phase_id}.objectifs` | `project_phases(id={phase_id}).objectifs` |
| Phase deliverables | `phases.{phase_id}.livrables` | `project_phases(id={phase_id}).livrables` |

Phase fields use the real `project_phases.id`, not the array index (`order`). This is intentional — if a proposer adds or removes a phase between the review and the resubmission, the address remains stable.

**Phase resources are deliberately excluded from field-level flagging.** Resources are a nested sub-list that changes shape dynamically. If the director needs a resource changed, flagging the whole `phases.{id}.livrables` or writing a general comment is sufficient. This avoids a three-level addressing scheme for now.

### PHP dot-mangling — confirmed safe

PHP mangles `.` and spaces to `_` in HTTP field names (both `$_GET` and `$_POST`). This means `field_key` values with dots must **never** be used verbatim as HTML `name` attributes.

The current forms already avoid this correctly — they use bracket notation (`phases[0][titre]`). On the revision page, the controller will resolve each `field_key` to a bracket-notation input name:

| `field_key` | Revision form `name` attribute |
|---|---|
| `title` | `revision[title]` |
| `evaluation.portee` | `revision[evaluation][portee]` |
| `phases.42.name` | `revision[phases][42][name]` |
| `phases.42.objectifs` | `revision[phases][42][objectifs][]` (repeatable list) |

This naming is safe through PHP and maps cleanly to Laravel dot-notation validation rules (`revision.phases.42.name`).

---

## 5. Review Submission Flow

### Route

```php
// One dedicated route per decision keeps authorization clear:
Route::post('/projects/{project}/review', [DirectionReviewController::class, 'store'])
    ->name('projects.review');
```

### Request payload (form POST)

```
decision          = "approved" | "denied" | "revision"
flagged_fields[]  = array of field_key strings (only when decision = "revision")
field_comments[{field_key}] = director's comment text for that field
general_comment   = optional free-text (any decision)
```

### Action: `SubmitReviewAction`

Matches the existing pattern in `app/Actions/CreateProjectProposal.php` — a single-method class wrapping a DB transaction, called from the controller.

```
app/Actions/SubmitReviewAction.php
```

Steps inside:
1. Create a `project_reviews` row: `{ project_id, user_id = auth()->id(), review_status = $decision }`
2. If `general_comment` is present: create a `comments` row with `field_key = null`, `requires_revision = false`, `stage = 'direction'`, `review_id`
3. For each entry in `flagged_fields` (only when `decision = 'revision'`): create a `comments` row with `field_key`, `content = field_comments[field_key]`, `requires_revision = true`, `stage = 'modification'`, `review_id`
4. Transition project state:
   - `approved` → `ProjectService::approve($project)` (already exists)
   - `denied` → `ProjectService::deny($project)` (already exists)
   - `revision` → `ProjectService::requestMoreInfo($project)` (already exists — rename to `requestRevision` when implementing)
5. If `revision`: dispatch `SendRevisionEmailJob::dispatch($project->proposer, $project)`

### Controller: `DirectionReviewController`

```
app/Http/Controllers/DirectionReviewController.php
```

Matches the pattern in `ProjectController` — thin, delegates to Action and Service:

```php
public function store(ReviewRequest $request, Project $project): RedirectResponse
{
    $this->authorize('review', $project); // gate to add
    app(SubmitReviewAction::class)->execute($request->validated(), $project);
    return Redirect::back()->with('status', 'review-submitted'de.);
}
```

### Mail: `RevisionRequestedEmail`

```
app/Mail/RevisionRequestedEmail.php
```

Matches `ApprovedEmail`/`DeniedEmail` exactly:
- Constructor takes `$name` (string) and `$project` (Project)
- Renders `resources/views/mail/RevisionRequested.blaphp`
- The view includes a link: `route('projects.revision', $project)` (a standard auth-protected URL — no signed URL needed; the proposer is a registered user who logs in normally)

### Job: `SendRevisionEmailJob`

```
app/Jobs/SendRevisionEmailJob.php
```

Matches `SendMailProcess` — implements `ShouldQueue`, takes `User $user` and `Project $project`, sends `RevisionRequestedEmail`.

---

## 6. Revision Page Flow

### Route

```php
Route::get('/projects/{project}/revision', [RevisionController::class, 'show'])
    ->middleware('auth')
    ->name('projects.revision');

Route::post('/projects/{project}/revision', [RevisionController::class, 'update'])
    ->middleware('auth')
    ->name('projects.revision.update');
```

Standard `auth` middleware is sufficient — the proposer is always a registered user.

### Controller: `RevisionController@show`

```
app/Http/Controllers/RevisionController.php
```

1. Authorize: `$project->proposer_id === auth()->id()` and `$project->status instanceof ModificationState`
2. Load unresolved revision comments:
   ```php
   $comments = $project->comments()
       ->where('requires_revision', true)
       ->whereNull('resolved_at')
       ->get();
   ```
3. Resolve each `field_key` to its current value (see resolution logic below)
4. Pass to view: `revision.blade.php`

### Field key resolution

A small private method (or dedicated `FieldKeyResolver` class) handles the three prefixes:

```
field_key prefix   → table + column
─────────────────────────────────────
(none / flat key)  → $project->{field_key}   (title, description, but, perimetre, ressources_totales)
"evaluation.*"     → $project->evaluation->{parts[1]}
"phases.{id}.*"    → ProjectPhase::find(parts[1])->{parts[2]}
```

The resolver returns `['key' => $field_key, 'current_value' => mixed, 'comment' => Comment, 'input_name' => string]` for each flagged field. `input_name` is the bracket-notation name to use in the revision form (see §4 table).

### Revision page view

```
resources/views/revision.blade.php
```

For each resolved field, renders:
- Director's comment in a highlighted box (read-only)
- The field's input (using the same component from `proposition/` if applicable, or a minimal equivalent)
- Only flagged fields — all others are hidden

### Controller: `RevisionController@update`

1. Validate only the revised fields (a `RevisionRequest` FormRequest, validation rules derived from the relevant subset of `PropositionRequest`)
2. Wrap in `DB::transaction`:
   a. Write values back to the real columns:
      - Flat keys → `$project->update([...])`
      - Evaluation keys → `$project->evaluation->update([...])`
      - Phase keys → `ProjectPhase::find($id)->update([...])`
   b. Mark each resolved comment: `$comment->update(['resolved_at' => now()])`
   c. Transition status: `ProjectService::reSubmit($project)` → back to `SubmittedState` (already exists)
3. Redirect to dashboard or propositions page

---

## 7. Risks and Conflicts Found

### Critical

**R1 — `requestMoreInfo` currently ignores its `comment` textarea.**
`testDirectionFront.blade.php:58–65` submits a `<textarea name="comment">` to `POST /projects/{project}/request-more-info`, but `ProjectController::requestMoreInfo()` (`app/Http/Controllers/ProjectController.php:39`) does not read or save that field. The comment is silently dropped. This must be fixed as part of this feature — the new `SubmitReviewAction` replaces that code path entirely.

**R2 — `project_reviews` is schema-only; nothing writes `review_status` today.**
`ProjectReview::$fillable` exists but no controller, service, or action creates a `project_reviews` row anywhere. This is safe (no existing values to conflict with), but it means `review_status` has no defined vocabulary yet. The new `approved`, `denied`, `revision` values will be the first ones ever written. Document them as a code comment in `ProjectReview`.

**R3 — Phase fields are addressed by DB `id`, but the creation form uses array index.**
`CreateProjectProposal` (`app/Actions/CreateProjectProposal.php:38`) creates phases in array order. The director's review page must load phases with their real IDs (`$project->phases` relationship ordered by `order`) so checkboxes can embed the correct phase ID in the `field_key`. This is only a concern for the director's review page rendering, not the existing form.

### Medium

**R4 — `current_stage` column is redundant with `status` but not kept in sync.**
`CreateProjectProposal` writes `current_stage = SubmittedState::getMorphClass()` alongside `status`, but `ProjectService` only calls `transitionTo()` on `status` — `current_stage` is never updated after the initial write. The revision flow should not rely on `current_stage`. Read `$project->status` only.

**R5 — No authorization policy exists yet.**
`PropositionRequest::authorize()` returns `true` unconditionally. `ProjectController` has no `$this->authorize()` calls. The revision page needs a guard that checks `$project->proposer_id === auth()->id() && $project->status instanceof ModificationState`, and the review page needs one that checks for a direction role. These guards need to be added (inline in controllers is acceptable given the current pattern, or in a `ProjectPolicy`).

**R6 — `Comment::$fillable` does not include the new columns.**
When the migration runs, the four new columns will exist in the DB but writes via `Comment::create([...])` or `$comment->update([...])` will silently ignore `field_key`, `requires_revision`, `resolved_at`, and `review_id` until they are added to `$fillable`. This is an implementation step, not a design conflict, but easy to miss.

### Low / Informational

**R7 — Blade form uses Alpine.js state, not raw HTML inputs, for most fields.**
On the revision page, Alpine.js is not needed — it's a targeted edit form, not a multi-step wizard. Use plain HTML inputs. Don't try to reuse the `x-proposition.*` wizard components (they depend on a global Alpine `x-data` object that tracks the wizard state).

**R8 — `but` and `objectifs`/`livrables` are JSON arrays.**
If the director flags `but` or `phases.{id}.objectifs`, the revision page must render a repeatable list input (like `x-proposition.repeatable-list` does) so the proposer can add/remove items. The submitted value will come through as a PHP array. Validate with `required|array|min:1` and each item `required|string`. On write, pass the whole array to `$project->update(['but' => $validated['but']])` — Eloquent's JSON cast handles serialization.

**R9 — Signed URLs are not needed but are available.**
`routes/auth.php:43` shows that the `signed` middleware is already in use for email verification. If a future requirement says the revision link must work without being logged in (e.g. for external proposers), switching to `URL::temporarySignedRoute('projects.revision', now()->addDays(7), $project)` is a one-line change. For now, plain `auth` middleware is correct.

---

## 8. New Files Summary

| File | Purpose |
|---|---|
| `database/migrations/…_extend_comments_for_field_review.php` | Adds 4 columns + index to `comments` |
| `app/Actions/SubmitReviewAction.php` | Creates review + field comments + transitions state |
| `app/Http/Requests/ReviewRequest.php` | Validates director's review POST |
| `app/Http/Requests/RevisionRequest.php` | Validates proposer's revision POST (dynamic — only flagged fields) |
| `app/Http/Controllers/DirectionReviewController.php` | Handles review submission |
| `app/Http/Controllers/RevisionController.php` | Shows revision page + handles update |
| `app/Mail/RevisionRequestedEmail.php` | Email to proposer |
| `app/Jobs/SendRevisionEmailJob.php` | Queued dispatch of revision email |
| `resources/views/direction/review.blade.php` | Director's field-level review form |
| `resources/views/revision.blade.php` | Proposer's filtered revision form |
| `resources/views/mail/RevisionRequested.blade.php` | Email body |

### Files that need changes

| File | Change needed |
|---|---|
| `app/Models/Comment.php` | Add 4 columns to `$fillable` |
| `app/Models/ProjectReview.php` | Add `review_id` to `$fillable`; document `review_status` values |
| `routes/web.php` | Add review + revision routes |
| `app/Http/Controllers/ProjectController.php` | `requestMoreInfo` becomes dead code — remove or redirect to new flow |
| `testDirectionFront.blade.php` | Temporary test view; replace with real direction review page |
