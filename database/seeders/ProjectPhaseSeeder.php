<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectPhaseSeeder extends Seeder
{
    public function run(): void
    {
        $projects = Project::all();

        if ($projects->isEmpty()) {
            $this->command->warn('No projects found. Run ProjectsSeeder first.');

            return;
        }

        $phasesData = [
            [
                'name' => 'Préparation',
                'duration' => '1 mois',
                'description' => 'Phase de préparation et de planification du projet.',
                'objectifs' => ['Définir le cadre', 'Mobiliser les acteurs', 'Planifier les actions'],
                'livrables' => ['Cahier des charges', 'Plan d\'action', 'Liste des participants'],
            ],
            [
                'name' => 'Mise en œuvre',
                'duration' => '3 mois',
                'description' => 'Phase de déploiement et de réalisation des actions.',
                'objectifs' => ['Réaliser les actions', 'Suivre les indicateurs', 'Ajuster le planning'],
                'livrables' => ['Rapports d\'étape', 'Indicateurs de suivi', 'Comptes rendus'],
            ],
            [
                'name' => 'Évaluation',
                'duration' => '1 mois',
                'description' => 'Phase d\'évaluation des résultats et de capitalisation.',
                'objectifs' => ['Évaluer l\'impact', 'Capitaliser les apprentissages', 'Préparer la suite'],
                'livrables' => ['Rapport final', 'Recommandations', 'Bilan financier'],
            ],
        ];

        foreach ($projects as $project) {
            foreach ($phasesData as $index => $phaseData) {
                $project->phases()->create(array_merge($phaseData, ['order' => $index + 1]));
            }
        }
    }
}
