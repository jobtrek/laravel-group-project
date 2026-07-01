<?php

namespace App\Http\Controllers;

use App\Enums\Stage;
use App\Models\Project;
use App\Models\States\EncoursState;
use App\Models\States\RecolteState;
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
        $project->loadMissing('phases.resources');
        
        if (! $project->status instanceof RecolteState) {
            return redirect()->back()->with('error', 'Project is not in Recolte state.');
        }

        if ($project->progress >= 80) {
            $project->status->transitionTo(EncoursState::class);
            $project->save();

            return redirect()->back()->with('success', 'Project moved to Active state successfully.');
        }

        return redirect()->back()->with('error', 'Project cannot be moved to Active state. Progress must be greater than 80%.');
    }
}
