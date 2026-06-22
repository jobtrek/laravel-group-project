<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    public function run(): void
    {
        $projects = Project::all();
        $users = User::all();

        if ($projects->isEmpty() || $users->isEmpty()) {
            return;
        }

        $comments = [
            'Le projet avance bien, l\'équipe est motivée.',
            'Nous avons rencontré quelques difficultés techniques, mais un plan de contournement est en place.',
            'Le budget est respecté pour l\'instant.',
            'Un partenaire supplémentaire s\'est engagé à nous soutenir.',
            'La phase de préparation est terminée, nous passons à la mise en œuvre.',
            'Les retours des bénéficiaires sont très positifs.',
            'Nous organisons une réunion de suivi la semaine prochaine.',
            'Un imprévu est survenu, le planning va être ajusté.',
            'Les objectifs de cette étape sont atteints.',
            'Merci à tous les bénévoles pour leur engagement.',
        ];

        $stages = ['proposition', 'direction', 'recolte', 'en_cours'];

        foreach ($projects as $project) {
            $commentCount = rand(1, 4);

            for ($i = 0; $i < $commentCount; $i++) {
                $project->comments()->create([
                    'content' => $comments[array_rand($comments)],
                    'stage' => $stages[array_rand($stages)],
                    'user_id' => $users->random()->id,
                ]);
            }
        }
    }
}
