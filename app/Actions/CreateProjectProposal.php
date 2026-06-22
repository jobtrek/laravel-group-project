<?php

// app/Actions/CreateProjectProposal.php

namespace App\Actions;

use App\Models\Project;
use App\Models\States\DraftState;
use App\Models\States\SubmittedState;
use Illuminate\Support\Facades\DB;

class CreateProjectProposal
{
    public function execute(array $data, int $proposerId): Project
    {
        return DB::transaction(function () use ($data, $proposerId) {
            $project = Project::create([
                'title' => $data['titre'],
                'description' => $data['description'],
                'but' => $data['buts'],
                'perimetre' => $data['perimetre'] ?? null,
                'ressources_totales' => $data['ressources_totales'] ?? null,
                'status' => SubmittedState::getMorphClass(),
                'current_stage' => SubmittedState::getMorphClass(),
                'proposer_id' => $proposerId,
                'leader_id' => $data['porteur'],
            ]);

            $project->members()->attach($data['membres']);

            $project->evaluation()->create([
                'portee' => $data['portee'],
                'impact' => $data['impact'],
                'confiance' => $data['confiance'],
                'effort' => $data['effort'],
            ]);

            foreach ($data['phases'] as $index => $phase) {
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
    }
}
