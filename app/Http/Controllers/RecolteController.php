<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Enums\Stage;
use App\Http\Requests\AssignProjectTeamRequest;
use App\Models\Project;
use App\Models\States\RecolteState;
use App\Models\User;
use App\Service\ProjectService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

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
        $leaderId = $request->validated('leader_id');

        DB::transaction(function () use ($project, $leaderId, $members) {
            $oldLeaderId = $project->leader_id;

            $project->update(['leader_id' => $leaderId]);
            $project->members()->sync($members);

            if ($leaderId) {
                User::find($leaderId)?->assignRole(Role::ChefDeProjet->value);
            }

            if ($oldLeaderId && $oldLeaderId !== $leaderId) {
                $oldLeader = User::find($oldLeaderId);
                if ($oldLeader && ! Project::where('leader_id', $oldLeaderId)->exists()) {
                    $oldLeader->removeRole(Role::ChefDeProjet->value);
                }
            }
        });

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
