<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Project;
use App\Models\ProjectPhase;
use App\Service\ProjectService;
use Illuminate\Support\Facades\DB;

class SubmitRevisionAction
{
    /**
     * @param  array<string, mixed>  $corrections  field_key => new value
     */
    public function execute(Project $project, array $corrections): void
    {
        DB::transaction(function () use ($project, $corrections): void {
            $project->load(['evaluation', 'phases.resources']);

            $projectDirty = false;
            $evaluationDirty = false;
            $phaseUpdates = []; // [phaseIndex => [attr => value]]

            $allowedKeys = $project->comments()
                ->where('stage', 'review')
                ->pluck('field_key')
                ->toArray();

            foreach ($corrections as $fieldKey => $value) {
                if (! in_array($fieldKey, $allowedKeys, true)) {
                    continue;
                }

                if (in_array($fieldKey, ['title', 'description', 'perimetre'], true)) {
                    $project->{$fieldKey} = $value;
                    $projectDirty = true;
                } elseif ($fieldKey === 'but') {
                    $project->but = array_values(array_filter((array) $value, fn ($v) => trim((string) $v) !== ''));
                    $projectDirty = true;
                } elseif (str_starts_with($fieldKey, 'evaluation.')) {
                    $attr = substr($fieldKey, strlen('evaluation.'));
                    if (in_array($attr, ['portee', 'impact', 'confiance', 'effort'], true) && $project->evaluation) {
                        $project->evaluation->{$attr} = $value;
                        $evaluationDirty = true;
                    }
                } elseif (str_starts_with($fieldKey, 'phases.')) {
                    $parts = explode('.', $fieldKey, 3);
                    if (count($parts) === 3) {
                        $phaseIndex = (int) $parts[1];
                        $attr = $parts[2];
                        $phaseUpdates[$phaseIndex][$attr] = $value;
                    }
                }
            }

            if ($projectDirty) {
                $project->save();
            }

            if ($evaluationDirty) {
                $project->evaluation->save();
            }

            $phases = $project->phases->values();

            foreach ($phaseUpdates as $phaseIndex => $attrs) {
                $phase = $phases[$phaseIndex] ?? null;
                if (! $phase) {
                    continue;
                }

                foreach ($attrs as $attr => $value) {
                    match ($attr) {
                        'titre' => $phase->name = $value,
                        'duree' => $phase->duration = $value,
                        'description' => $phase->description = $value,
                        'objectifs' => $phase->objectifs = array_values(array_filter((array) $value, fn ($v) => trim((string) $v) !== '')),
                        'livrables' => $phase->livrables = array_values(array_filter((array) $value, fn ($v) => trim((string) $v) !== '')),
                        'ressources' => $this->replaceResources($phase, (array) $value),
                        default => null,
                    };
                }

                $phase->save();
            }

            $project->comments()->where('stage', 'review')->delete();

            ProjectService::reSubmit($project);
        });
    }

    /** @param array<int, array{resource_type: string, amount_needed: string}> $resources */
    private function replaceResources(ProjectPhase $phase, array $resources): void
    {
        $phase->resources()->delete();

        foreach ($resources as $resource) {
            if (! is_array($resource) || empty($resource['resource_type'])) {
                continue;
            }

            $phase->resources()->create([
                'resource_type' => $resource['resource_type'],
                'amount_needed' => $resource['amount_needed'] ?? 0,
            ]);
        }
    }
}
