<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\States\ActiveState;



class RecolteController extends Controller
{
    public function moveFromRecolteToActive(Request $request)
    {
        $recolteId = $request->input('recolte_id');
        $project = Project::findOrFail($recolteId);

        if ($project->progress >= 80) {
            $project->status = ActiveState::class;
            $project->save();

            return redirect()->back()->with('success', 'Project moved to Active state successfully.');
        } else {
            return redirect()->back()->with('error', 'Project cannot be moved to Active state. Progress must be greater than 80%.');
        }
    }
}
