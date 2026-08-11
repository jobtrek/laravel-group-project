<?php

namespace App\Policies;

use App\Models\Project;
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
    public function review(User $user, Project $project): bool
    {
        return $project->proposer_id !== $user->id;
    }
}
