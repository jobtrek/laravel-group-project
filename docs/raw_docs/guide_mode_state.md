# State Machine — Developer Guide

This guide explains how to work with the `Project` state machine in code. It is intentionally
practical: every section is a snippet you can copy and adapt. For the business rules and
requirements behind each state, see `docs/states.md`.

> **Package:** `spatie/laravel-model-states`
> **Column:** `projects.status` (cast to `ProjectState`)
> **Default state:** `DraftState`

---

## State Map

```
DraftState
  └── [proposer submits] ──────────────────────────► SubmittedState
                                                           │
                    ┌──────────────────────────────────────┤
                    │                                      │
          [direction approves]               [direction refuses / suspends / archives]
                    │                                      │
                    ▼                             RefusedState (le frigo)
             ApprovedState ─[auto]──────────────► ModificationState
                    │                                      │
                  [auto]                       [proposer resubmits] ──► SubmittedState
                    │
                    ▼
             CollectingState ─[≥80% resources]──► ReadyState
                    │                                  │
           [12 months]                        [manual launch]
                    │                                  │
                    ▼                                  ▼
             ArchivedState ◄──────────── ActiveState ─[complete]─► CompletedState
                    │                        │
             [manual restore]          [3 months no comment]
                    │                        │
                    ▼                        ▼
       SubmittedState / CollectingState / ActiveState    ArchivedState
```

**Allowed transitions (from `Project::registerStates()`):**

| From                | To                                                                    |
|---------------------|-----------------------------------------------------------------------|
| `DraftState`        | `SubmittedState`                                                      |
| `SubmittedState`    | `ApprovedState`, `RefusedState`, `ModificationState`, `ArchivedState` |
| `ModificationState` | `SubmittedState`, `ArchivedState`                                     |
| `ApprovedState`     | `CollectingState`                                                     |
| `RefusedState`      | `SubmittedState`                                                      |
| `CollectingState`   | `ReadyState`, `ArchivedState`                                         |
| `ReadyState`        | `ActiveState`, `CollectingState`, `ArchivedState`                     |
| `ActiveState`       | `CompletedState`, `ArchivedState`                                     |
| `ArchivedState`     | `SubmittedState`, `CollectingState`, `ActiveState`                    |

---

## 1. Triggering a Transition (Backend)

### Basic transition

```php
use App\Models\States\SubmittedState;

// Transition and save in one call
$project->status->transitionTo(SubmittedState::class);
```

This persists the new state to the database immediately. If the transition is not
allowed by `registerStates()`, Spatie throws a `TransitionNotAllowed` exception.

### Guard before transitioning

```php
if ($project->status->canTransitionTo(SubmittedState::class)) {
    $project->status->transitionTo(SubmittedState::class);
}
```

### Using a custom Transition class (recommended for complex transitions)

When a transition needs extra work (set timestamps, fire events, send mail), create
a dedicated class instead of doing it inline:

```php
// app/Models/Transitions/DraftToSubmittedTransition.php
use Spatie\ModelStates\Transition;

class DraftToSubmittedTransition extends Transition
{
    public function handle(): Project
    {
        $this->model->submitted_at = now();
        $this->model->save();

        // fire an event, send a notification, etc.
        return $this->model;
    }
}
```

Register it in `Project::registerStates()`:

```php
->allowTransition(DraftState::class, SubmittedState::class, DraftToSubmittedTransition::class)
```

Then call it the same way — Spatie picks the registered class automatically:

```php
$project->status->transitionTo(SubmittedState::class);
```

---

## 2. Triggering a Transition (Frontend → Controller)

The typical flow is: **button/form in Blade → POST route → controller method → `transitionTo`**.

### Route

```php
// routes/web.php
Route::post('/projects/{project}/submit',  [ProjectController::class, 'submit'])->name('projects.submit');
Route::post('/projects/{project}/approve', [ProjectController::class, 'approve'])->name('projects.approve');
Route::post('/projects/{project}/refuse',  [ProjectController::class, 'refuse'])->name('projects.refuse');
// etc.
```

One dedicated route per transition keeps authorisation simple and each action explicit.

### Controller

```php
public function submit(Project $project): RedirectResponse
{
    // Authorise (see section 5)
    $this->authorize('submit', $project);

    $project->status->transitionTo(SubmittedState::class);

    return redirect()->route('projects.show', $project)
        ->with('success', 'Proposition soumise.');
}

public function approve(Request $request, Project $project): RedirectResponse
{
    $this->authorize('approve', $project);

    // ApprovedState auto-advances to RecolteState (see section 6)
    $project->status->transitionTo(ApprovedState::class);

    return redirect()->back();
}
```

### Blade button

```blade
{{-- Only show the submit button when the project is in Draft --}}
@if ($project->status instanceof \App\Models\States\DraftState)
    <form method="POST" action="{{ route('projects.submit', $project) }}">
        @csrf
        <button type="submit">Soumettre</button>
    </form>
@endif
```

---

## 3. Querying by State

Spatie adds `whereState` and `whereNotState` scopes automatically.

### Fetch all projects in a single state

```php
use App\Models\States\SubmittedState;

$pending = Project::whereState('status', SubmittedState::class)->get();
```

### Fetch projects in any of several states

```php
use App\Models\States\{RecolteState, ReadyState};

$inRecolte = Project::whereState('status', [RecolteState::class, ReadyState::class])->get();
```

### Exclude a state

```php
use App\Models\States\ArchiveState;

$active = Project::whereNotState('status', ArchiveState::class)->get();
```

### Combine with other scopes

```php
$overdueSubmissions = Project::whereState('status', SubmittedState::class)
    ->where('updated_at', '<', now()->subMonths(3))
    ->get();
```

### Order the direction queue (highest evaluation score first)

```php
$queue = Project::whereState('status', SubmittedState::class)
    ->with('evaluation')
    ->join('project_evaluations', 'projects.id', '=', 'project_evaluations.project_id')
    ->orderByRaw('(portee + impact + confiance - effort) DESC')
    ->select('projects.*')
    ->get();
```

---

## 4. Checking State in PHP Logic

### instanceof check (preferred for a single state)

```php
if ($project->status instanceof DraftState) {
    // only proposer can edit
}
```

### `equals()` helper

```php
if ($project->status->equals(DraftState::class)) { /* … */ }
```

### `isOneOf()` for multiple states

```php
use App\Models\States\{RecolteState, ReadyState};

if ($project->status->isOneOf([RecolteState::class, ReadyState::class])) {
    // show the récolte panel
}
```

### Boolean helpers on the model (optional, but cleans up Blade)

Add these to `Project.php`:

```php
public function isDraft(): bool        { return $this->status instanceof DraftState; }
public function isSubmitted(): bool    { return $this->status instanceof SubmittedState; }
public function isInRecolte(): bool    { return $this->status->isOneOf([CollectingState::class, ReadyState::class]); }
public function isArchived(): bool     { return $this->status instanceof ArchivedState; }
// etc.
```

Then in Blade:

```blade
@if ($project->isDraft())
    <a href="{{ route('projects.edit', $project) }}">Modifier</a>
@endif
```

---

## 5. Gate / Policy Logic Based on State

State checks belong in a `ProjectPolicy` so the same rule is enforced in
controllers and Blade without duplication.

```php
// app/Policies/ProjectPolicy.php

public function submit(User $user, Project $project): bool
{
    return $user->id === $project->proposer_id
        && $project->status->canTransitionTo(SubmittedState::class);
}

public function approve(User $user, Project $project): bool
{
    // Only Direction / RH, and only when Submitted
    return $user->isDirection()
        && $project->status instanceof SubmittedState;
}

public function edit(User $user, Project $project): bool
{
    // Only the proposer can edit, and only in Draft or Modification
    return $user->id === $project->proposer_id
        && $project->status->isOneOf([DraftState::class, ModificationState::class]);
}
```

Controller:

```php
$this->authorize('submit', $project);
```

Blade:

```blade
@can('edit', $project)
    <a href="{{ route('projects.edit', $project) }}">Modifier</a>
@endcan
```

---

## 6. Automatic Transitions

Automatic transitions are state changes the system makes on its own, triggered by
an event or by a scheduled job.

### A. Auto-advance on approval (event-driven)

`ApprovedState` is a transient state — the project should jump straight to
`CollectingState` the moment Direction approves. Do this in the Transition class
or in a model observer.

**Option 1 — inside the Transition class:**

```php
class SubmittedToApprovedTransition extends Transition
{
    public function handle(): Project
    {
        $this->model->save(); // persist ApprovedState first (for audit)
        $this->model->status->transitionTo(CollectingState::class); // then advance
        return $this->model;
    }
}
```

**Option 2 — model observer:**

```php
// app/Observers/ProjectObserver.php
use Spatie\ModelStates\Events\StateChanged;

class ProjectObserver
{
    public function updated(Project $project): void
    {
        if ($project->wasChanged('status') && $project->status instanceof ApprovedState) {
            $project->status->transitionTo(CollectingState::class);
        }
    }
}
```

Register the observer in `AppServiceProvider::boot()`:

```php
Project::observe(ProjectObserver::class);
```

### B. Auto-advance when resources reach 80% (event-driven)

Trigger this whenever a `PhaseResource` row is updated:

```php
// app/Observers/PhaseResourceObserver.php

public function saved(PhaseResource $resource): void
{
    $project = $resource->phase->project;

    if (! $project->status instanceof CollectingState) {
        return;
    }

    $total   = $project->phases->flatMap->resources->sum('amount_needed');
    $found   = $project->phases->flatMap->resources->sum('amount_found');
    $percent = $total > 0 ? $found / $total : 0;

    if ($percent >= 0.80) {
        $project->status->transitionTo(ReadyState::class);
    }
}
```

And if resources drop back below 80%:

```php
if ($project->status instanceof ReadyState && $percent < 0.80) {
    $project->status->transitionTo(CollectingState::class);
}
```

### C. Time-based auto-archive (scheduled job)

Create a command and schedule it daily:

```bash
php artisan make:command AutoArchiveProjects
```

```php
// app/Console/Commands/AutoArchiveProjects.php

public function handle(): void
{
    // Proposition: 3 months without direction action
    Project::whereState('status', [SubmittedState::class, ModificationState::class])
        ->where('updated_at', '<', now()->subMonths(3))
        ->each(fn (Project $p) => $p->status->transitionTo(ArchivedState::class));

    // Récolte: 12 months
    Project::whereState('status', [CollectingState::class, ReadyState::class])
        ->where('updated_at', '<', now()->subMonths(12))
        ->each(fn (Project $p) => $p->status->transitionTo(ArchivedState::class));

    // En cours: 3 months without a comment
    Project::whereState('status', ActiveState::class)
        ->whereDoesntHave('comments', fn ($q) => $q->where('created_at', '>', now()->subMonths(3)))
        ->each(fn (Project $p) => $p->status->transitionTo(ArchivedState::class));
}
```

Register in `routes/console.php` (Laravel 11+):

```php
Schedule::command(AutoArchiveProjects::class)->daily();
```

### D. Email escalation for inactive active projects

Same pattern — a scheduled command that fires emails rather than transitions:

```php
// Projects active for 1 month without a comment → email #1
Project::whereState('status', ActiveState::class)
    ->whereNull('last_reminder_at')
    ->whereDoesntHave('comments', fn ($q) => $q->where('created_at', '>', now()->subMonth()))
    ->each(function (Project $p) {
        $p->leader->notify(new InactiveProjectReminderNotification($p));
        $p->update(['last_reminder_at' => now()]);
    });

// +1 week after first email, still no comment → email #2 (CC everyone)
Project::whereState('status', ActiveState::class)
    ->whereNotNull('last_reminder_at')
    ->where('last_reminder_at', '<', now()->subWeek())
    ->whereDoesntHave('comments', fn ($q) => $q->where('created_at', '>', now()->subMonth()))
    ->each(function (Project $p) {
        Notification::send($p->members, new InactiveProjectEscalationNotification($p));
    });
```

---

## 7. Listening to State Changes (Events)

Spatie fires a `StateChanged` event on every transition. Use it to hook in
cross-cutting concerns (notifications, audit log, etc.) without cluttering
your controllers.

```php
// app/Listeners/ProjectStateChangedListener.php
use Spatie\ModelStates\Events\StateChanged;

class ProjectStateChangedListener
{
    public function handle(StateChanged $event): void
    {
        $project      = $event->model;           // the Project
        $fromState    = $event->initialState;    // e.g. DraftState instance
        $toState      = $event->finalState;      // e.g. SubmittedState instance

        // Example: notify the leader when a project is submitted
        if ($toState instanceof SubmittedState) {
            $project->leader?->notify(new ProjectSubmittedNotification($project));
        }
    }
}
```

Register in `AppServiceProvider` or `EventServiceProvider`:

```php
Event::listen(StateChanged::class, ProjectStateChangedListener::class);
```

---

## 8. Quick Reference

### "Which state am I in?"

```php
$project->status->label();          // → 'draft', 'submitted', etc.
(string) $project->status;          // same
$project->status instanceof DraftState; // → true / false
```

### "Can I go to X from here?"

```php
$project->status->canTransitionTo(SubmittedState::class); // → bool
```

### "Move to X"

```php
$project->status->transitionTo(SubmittedState::class); // saves immediately
```

### "Get all projects in state X"

```php
Project::whereState('status', SubmittedState::class)->get();
```

### "Get all projects NOT in state X"

```php
Project::whereNotState('status', ArchivedState::class)->get();
```

### "What triggers auto-archive?"

| State               | Trigger                        | Threshold |
|---------------------|--------------------------------|-----------|
| `SubmittedState`    | No direction action            | 3 months  |
| `ModificationState` | No proposer revision           | 3 months  |
| `CollectingState`   | No resources & time elapsed    | 12 months |
| `ReadyState`        | Time elapsed (récolte horizon) | 12 months |
| `ActiveState`       | No comment added               | 3 months  |
