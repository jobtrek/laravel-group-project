<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectsSeeder extends Seeder
{
    public function run(): void
    {
        $userIds = User::pluck('id')->toArray();

        if (empty($userIds)) {
            $this->command->warn('No users found. Run UserSeeder first.');

            return;
        }

        $states = ['draft', 'submitted', 'modification', 'approved', 'refused', 'collecting', 'ready', 'active', 'completed', 'archived'];

        $projects = [
            [
                'title' => 'Jardin Communautaire',
                'description' => 'Créer un jardin partagé dans le quartier nord pour favoriser le lien social et l\'accès à une alimentation saine.',
                'budget_global' => 25000.00,
                'but' => ['objectif' => 'Favoriser le lien social et l\'alimentation saine', 'priorite' => 'haute'],
                'perimetre' => 'Quartier nord, 200m², 50 familles bénéficiaires',
                'ressources_totales' => 'Terrain municipal, outils de jardinage, semences, système d\'irrigation',
                'status' => 'active',
                'current_stage' => 'active',
                'proposer_id' => $userIds[array_rand($userIds)],
                'leader_id' => $userIds[array_rand($userIds)],
                'recolte_manager_id' => $userIds[array_rand($userIds)],
            ],
            [
                'title' => 'Ateliers Numériques pour Seniors',
                'description' => 'Proposer des ateliers d\'initiation au numérique pour les personnes âgées de la commune.',
                'budget_global' => 12000.00,
                'but' => ['objectif' => 'Réduire la fracture numérique chez les seniors', 'priorite' => 'moyenne'],
                'perimetre' => 'Maison de quartier, 10 sessions, 30 participants',
                'ressources_totales' => '10 tablettes, connexion internet, supports pédagogiques',
                'status' => 'collecting',
                'current_stage' => 'collecting',
                'proposer_id' => $userIds[array_rand($userIds)],
                'leader_id' => $userIds[array_rand($userIds)],
                'recolte_manager_id' => $userIds[array_rand($userIds)],
            ],
            [
                'title' => 'Festival de la Transition Écologique',
                'description' => 'Organiser un festival sur trois jours pour sensibiliser aux enjeux écologiques et promouvoir les initiatives locales.',
                'budget_global' => 80000.00,
                'but' => ['objectif' => 'Sensibilisation à l\'écologie', 'priorite' => 'haute'],
                'perimetre' => 'Parc municipal, 3 jours, 2000 visiteurs attendus',
                'ressources_totales' => 'Scène, sonorisation, stands, bénévoles, communication',
                'status' => 'draft',
                'current_stage' => 'draft',
                'proposer_id' => $userIds[array_rand($userIds)],
                'leader_id' => null,
                'recolte_manager_id' => null,
            ],
            [
                'title' => 'Bibliothèque Mobile',
                'description' => 'Mettre en place un bibliobus pour desservir les zones rurales éloignées des infrastructures culturelles.',
                'budget_global' => 45000.00,
                'but' => ['objectif' => 'Accès à la culture en zone rurale', 'priorite' => 'moyenne'],
                'perimetre' => '5 communes rurales, 1 véhicule aménagé, 5000 livres',
                'ressources_totales' => 'Camion aménagé, fonds documentaire, carburant, bibliothécaire',
                'status' => 'submitted',
                'current_stage' => 'submitted',
                'proposer_id' => $userIds[array_rand($userIds)],
                'leader_id' => null,
                'recolte_manager_id' => null,
            ],
            [
                'title' => 'Cantine Solidaire',
                'description' => 'Créer une cantine à prix libre dans le centre-ville pour lutter contre la précarité alimentaire.',
                'budget_global' => 60000.00,
                'but' => ['objectif' => 'Lutte contre la précarité alimentaire', 'priorite' => 'haute'],
                'perimetre' => 'Centre-ville, 100 repas/jour, 7j/7',
                'ressources_totales' => 'Local cuisine, équipement professionnel, denrées, cuisiniers, bénévoles',
                'status' => 'ready',
                'current_stage' => 'ready',
                'proposer_id' => $userIds[array_rand($userIds)],
                'leader_id' => $userIds[array_rand($userIds)],
                'recolte_manager_id' => $userIds[array_rand($userIds)],
            ],
            [
                'title' => 'Réseau de Compostage Participatif',
                'description' => 'Déployer des bornes de compostage partagé dans les résidences et suivre la réduction des déchets.',
                'budget_global' => 18000.00,
                'but' => ['objectif' => 'Réduction des déchets organiques', 'priorite' => 'basse'],
                'perimetre' => '10 résidences, 300 foyers',
                'ressources_totales' => 'Borne de compostage, bio-seaux, formation',
                'status' => 'refused',
                'current_stage' => 'refused',
                'proposer_id' => $userIds[array_rand($userIds)],
                'leader_id' => null,
                'recolte_manager_id' => null,
            ],
            [
                'title' => 'Plateforme de Mentorat Jeunes',
                'description' => 'Développer une plateforme mettant en relation des jeunes en recherche d\'orientation avec des professionnels mentor.',
                'budget_global' => 35000.00,
                'but' => ['objectif' => 'Soutien à l\'orientation professionnelle', 'priorite' => 'haute'],
                'perimetre' => 'Département, 100 binômes mentor-mentoré',
                'ressources_totales' => 'Plateforme web, bases de données, communication, événements',
                'status' => 'approved',
                'current_stage' => 'approved',
                'proposer_id' => $userIds[array_rand($userIds)],
                'leader_id' => $userIds[array_rand($userIds)],
                'recolte_manager_id' => null,
            ],
            [
                'title' => 'Rénovation du Centre Sportif',
                'description' => 'Rénover le centre sportif municipal pour le rendre accessible aux personnes à mobilité réduite.',
                'budget_global' => 150000.00,
                'but' => ['objectif' => 'Accessibilité universelle du sport', 'priorite' => 'haute'],
                'perimetre' => 'Centre sportif municipal, 6 mois de travaux',
                'ressources_totales' => 'Matériaux, architecte, entreprise spécialisée, équipements adaptés',
                'status' => 'modification',
                'current_stage' => 'modification',
                'proposer_id' => $userIds[array_rand($userIds)],
                'leader_id' => $userIds[array_rand($userIds)],
                'recolte_manager_id' => null,
            ],
            [
                'title' => 'Ateliers de Réinsertion Professionnelle',
                'description' => 'Proposer des ateliers de remise à niveau et de développement de compétences pour les demandeurs d\'emploi longue durée.',
                'budget_global' => 55000.00,
                'but' => ['objectif' => 'Réinsertion professionnelle', 'priorite' => 'haute'],
                'perimetre' => 'Pôle emploi, 50 bénéficiaires, 6 mois',
                'ressources_totales' => 'Formateurs, matériel pédagogique, salle, ordinateurs',
                'status' => 'completed',
                'current_stage' => 'completed',
                'proposer_id' => $userIds[array_rand($userIds)],
                'leader_id' => $userIds[array_rand($userIds)],
                'recolte_manager_id' => $userIds[array_rand($userIds)],
            ],
            [
                'title' => 'Exposition d\'Art Urbain',
                'description' => 'Organiser une exposition éphémère d\'art urbain dans les rues du centre-ville avec des artistes locaux.',
                'budget_global' => 22000.00,
                'but' => ['objectif' => 'Promotion de l\'art urbain local', 'priorite' => 'basse'],
                'perimetre' => 'Centre-ville, 15 artistes, 2 semaines',
                'ressources_totales' => 'Murs autorisés, matériel, assurance, communication',
                'status' => 'archived',
                'current_stage' => 'archived',
                'proposer_id' => $userIds[array_rand($userIds)],
                'leader_id' => null,
                'recolte_manager_id' => null,
            ],
        ];

        foreach ($projects as $data) {
            Project::create($data);
        }
    }
}
