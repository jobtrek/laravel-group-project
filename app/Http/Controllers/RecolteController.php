<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\States\CollectingState;

class RecolteController extends Controller
{
    public function recolteProgress()
    {
        // 1. take all projects with status "Collecting" and load their phases and resources
        $projects = Project::whereState('status', CollectingState::class)
            ->with('phases.resources')
            ->get();

        return view('recolte', ['projects' => $projects]);
    }
}
