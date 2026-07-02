<?php

namespace App\Http\Controllers;

use App\Enums\Stage;
use App\Models\Project;
use App\Models\States\RecolteState;
use App\Service\ProjectService;
use Illuminate\Http\RedirectResponse;

class RecolteController extends StageProjectController
{
    protected function stage(): Stage
    {
        return Stage::Recolte;
    }

    protected function states(): string|array
    {
        return [RecolteState::class];
    }

    public function moveFromRecolteToActive(Project $project): RedirectResponse
    {
        $project->loadMissing(['phases.resources', 'phases.contributions']);

        if (! ProjectService::moveToEncours($project)) {
            return redirect()->back()->with('error', 'Project cannot be moved to Active state. It must be in Recolte state with progress of at least 80%.');
        }

        return redirect()->back()->with('success', 'Project moved to Active state successfully.');
    }
}
