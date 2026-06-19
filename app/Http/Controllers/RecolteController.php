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

        // 2. go through each project and calculate the progress based on the resources needed and found
        foreach ($projects as $project) {
            $totalNeeded = 0;
            $totalFound = 0;
            $progress = 0;

            foreach ($project->phases as $phase) {

                foreach ($phase->resources as $resource) {

                    $totalNeeded += (float) $resource->amount_needed;
                    $totalFound += (float) ($resource->amount_found ?? 0);
                }
            }

            // 5. set progress to 0 if totalNeeded is 0 to avoid division by zero
            if ($totalNeeded <= 0) {
                $project['progress'] = 0;

                continue;
            }

            // 6. calculate progress percentage
            $progress = ($totalFound / $totalNeeded) * 100;

            // 7. round progress to 2 decimal places and add it to the project object
            $progress = round($progress, 2);
            $project['progress'] = $progress;
        }

        return view('dashboard', ['projects' => $projects]);
    }
}
