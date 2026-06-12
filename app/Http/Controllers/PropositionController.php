<?php

namespace App\Http\Controllers;

use App\Http\Requests\PropositionRequest;
use App\Models\Project;
use Illuminate\Support\Facades\DB;

class PropositionController extends Controller
{
    public function store(PropositionRequest $request)
    {
        $safe = $request->safe();

        $step_1 = $safe->only(['titre', 'porteur', 'membres', 'description', 'buts', 'perimetre']);
        $step_2 = $safe->only(['phases', 'ressources_totales']);
        $step_3 = $safe->only(['portee', 'impact', 'confiance', 'effort']);

        DB::transaction(function () use ($step_1, $step_2, $step_3) {

            $project = Project::create([
                'title' => $step_1['titre'],
                'description' => $step_1['description'],
                'but' => $step_1['buts'],
                'perimetre' => $step_1['perimetre'],
                'status' => 'proposition',
                'current_stage' => 'proposition',
                'proposer_id' => auth()->id(),
                'leader_id' => $step_1['porteur'],
            ]);

            $project->members()->attach($step_1['membres']);

            $project->evaluation()->create([
                'portee' => $step_3['portee'],
                'impact' => $step_3['impact'],
                'confiance' => $step_3['confiance'],
                'effort' => $step_3['effort'],
            ]);
            foreach ($step_2['phases'] as $index => $phase) {
                $createdPhase = $project->phases()->create([
                    'name' => $phase['titre'],
                    'duration' => $phase['duree'],
                    'description' => $phase['description'],
                    'objectifs' => $phase['objectifs'],
                    'livrables' => $phase['livrables'],
                    'order' => $index + 1,
                ]);

                foreach ($phase['ressources_necessaires'] as $resource) {
                    $createdPhase->resources()->create([
                        'resource_type' => $resource['resource_type'],
                        'amount_needed' => $resource['amount_needed'],
                    ]);
                }
            }

            return $project;

        });

        return redirect()->route('dashboard');
    }
}
