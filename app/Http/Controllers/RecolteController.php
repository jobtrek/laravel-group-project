<?php

namespace App\Http\Controllers;

use App\Enums\Stage;
use App\Http\Requests\AssignProjectTeamRequest;
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

    public function assignTeam(AssignProjectTeamRequest $request, Project $project): RedirectResponse
    {
        abort_if(! $project->status instanceof RecolteState, 404);

        $members = array_filter($request->validated('membres', []));

        $project->update(['leader_id' => $request->validated('leader_id')]);
        $project->members()->sync($members);

        return redirect()->back()->with('success', 'Équipe du projet mise à jour avec succès.');
    }

    public function moveFromRecolteToActive(Project $project): RedirectResponse
    {
        abort_if($project->leader_id !== auth()->id() && ! auth()->user()?->can('manage everything'), 403);

        if (! ProjectService::moveToEncours($project)) {
            return redirect()->back()->with('error', 'Project cannot be moved to Active state. It must be in Recolte state with a project chief assigned.');
        }

        return redirect()->route('en-cours')->with('success', 'Project moved to Active state successfully.');
    }
}
