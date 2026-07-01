<?php

namespace App\Http\Controllers;

use App\Enums\Stage;
use App\Models\PhaseResource;
use App\Models\Project;
use App\Models\States\EncoursState;
use App\Models\States\RecolteState;
use Illuminate\Http\Request;

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

    public function moveFromRecolteToActive(Request $request)
    {
        $recolteId = $request->input('recolte_id');
        $project = Project::findOrFail($recolteId);

        if ($project->progress >= 80) {
            $project->status->transitionTo(EncoursState::class);
            $project->save();

            return redirect()->back()->with('success', 'Project moved to Active state successfully.');
        } else {
            return redirect()->back()->with('error', 'Project cannot be moved to Active state. Progress must be greater than 80%.');
        }
    }

    public function updateProgress(Request $request)
    {
        $request->validate([
            'phase_resource_id' => 'required|exists:phase_resources,id',
            'amount_found' => 'required|numeric|min:0',
        ]);
        $phaseResourceId = $request->input('phase_resource_id');

        $resource = PhaseResource::findOrFail($phaseResourceId);

        $resource->update([
            'amount_found' => $request->input('amount_found'),
        ]);

        return redirect()->back()->with('success', 'Project progress updated successfully.');
    }
}
