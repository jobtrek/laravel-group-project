<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Project;
use App\Models\States\EncoursState;
use App\Models\States\RevisionState;
use App\Models\User;

class ProjectPolicy
{
    /**
     * Determine whether the user may review the project (approve, deny, or
     * request more info). This is the single source of truth for the rule
     * that direction members cannot review their own proposals.
     *
     * Role-based access (whether the user may review *any* project at all)
     * is still enforced separately by the `can:approve` / `can:deny` /
     * `can:review` route middleware. This policy only guards ownership.
     *
     * Users with the "manage everything" permission bypass this check via
     * the global Gate::before hook in AppServiceProvider.
     */
    public function reviewOwn(User $user, Project $project): bool
    {
        return $project->proposer_id !== $user->id;
    }

    public function comment(User $user, Project $project): bool
    {
        if ($user->can('manage everything')) {
            return true;
        }

        return $project->status instanceof EncoursState
            && $user->hasRole(Role::ChefDeProjet->value)
            && $user->id === $project->leader_id;
    }

    /**
     * Owner of a revision-requested project may resubmit it.
     */
    public function resubmit(User $user, Project $project): bool
    {
        return $project->proposer_id === $user->id
            && $project->status instanceof RevisionState;
    }

    /**
     * Owner of an editable project may edit it.
     */
    public function update(User $user, Project $project): bool
    {
        return $project->status->isEditable()
            && $user->id === $project->proposer_id;
    }

    /**
     * The project leader or a project manager may mark a fully-funded
     * En cours project as complete. The `complete project` permission check
     * is also duplicated here (on top of the `can:complete project` route
     * middleware) so `@can('complete', $project)` in Blade hides the button
     * for users without it, not just the route.
     */
    public function complete(User $user, Project $project): bool
    {
        return $project->status instanceof EncoursState
            && $project->progress >= 100
            && $user->can('complete project')
            && ($user->id === $project->leader_id || $user->hasRole(Role::ProjectManager->value));
    }

    /**
     * Owner of a project in revision may view/submit the revision form.
     */
    public function submitRevision(User $user, Project $project): bool
    {
        return $project->proposer_id === $user->id;
    }
}
