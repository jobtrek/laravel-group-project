<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\PhaseResource;
use App\Models\Project;
use App\Models\ProjectPhase;
use Illuminate\Support\Facades\DB;

class UpdateProjectAction
{
    public function execute(Project $project, array $data): Project
    {
        return DB::transaction(function () use ($project, $data) {
            $project->update([
                'title' => $data['title'],
                'description' => $data['description'],
                'perimetre' => $data['perimetre'] ?? null,
                'but' => $data['but'],
            ]);

            $project->members()->sync($data['membres']);

            $project->evaluation->update([
                'portee' => $data['portee'],
                'impact' => $data['impact'],
                'confiance' => $data['confiance'],
                'effort' => $data['effort'],
            ]);

            $this->syncPhases($project, $data['phases']);

            return $project->fresh(['phases.resources', 'evaluation', 'members']);
        });
    }

    private function syncPhases(Project $project, array $phasesData): void
    {
        $keptIds = [];

        foreach ($phasesData as $index => $phaseData) {
            $attributes = [
                'name' => $phaseData['titre'],
                'duration' => $phaseData['duree'],
                'description' => $phaseData['description'],
                'objectifs' => $phaseData['objectifs'],
                'livrables' => $phaseData['livrables'],
                'order' => $index + 1,
            ];

            $phase = ! empty($phaseData['id'])
                ? $project->phases->firstWhere('id', $phaseData['id'])
                : null;

            if ($phase) {
                $phase->update($attributes);
            } else {
                $phase = $project->phases()->create($attributes);
            }

            if (! $phase) {
                continue;
            }

            $keptIds[] = $phase->id;
            $this->syncResources($phase, $phaseData['ressources'] ?? []);
        }

        $project->phases()
            ->whereNotIn('id', $keptIds)
            ->get()
            ->each(fn (ProjectPhase $phase) => $phase->delete());
    }

    private function syncResources(ProjectPhase $phase, array $resourcesData): void
    {
        $keptIds = [];

        foreach ($resourcesData as $resourceData) {
            $attributes = [
                'resource_type' => $resourceData['resource_type'],
                'amount_needed' => $resourceData['amount_needed'],
            ];

            $resource = ! empty($resourceData['id'])
                ? $phase->resources->firstWhere('id', $resourceData['id'])
                : null;

            if ($resource) {
                $resource->update($attributes);
            } else {
                $resource = $phase->resources()->create($attributes);
            }

            $keptIds[] = $resource->id;
        }

        $phase->resources()
            ->whereNotIn('id', $keptIds)
            ->get()
            ->each(fn (PhaseResource $resource) => $resource->delete());
    }
}
