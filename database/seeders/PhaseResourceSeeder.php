<?php

namespace Database\Seeders;

use App\Models\ProjectPhase;
use Illuminate\Database\Seeder;

class PhaseResourceSeeder extends Seeder
{
    public function run(): void
    {
        $phases = ProjectPhase::all();

        if ($phases->isEmpty()) {
            $this->command->warn('No phases found. Run ProjectPhaseSeeder first.');

            return;
        }

        $resourcesByPhase = [
            0 => [
                ['resource_type' => 'budget', 'amount_needed' => 5000.00, 'amount_found' => 3000.00, 'description' => 'Budget préparation'],
                ['resource_type' => 'humain', 'amount_needed' => 2000.00, 'amount_found' => 2000.00, 'description' => 'Ressources humaines'],
            ],
            1 => [
                ['resource_type' => 'budget', 'amount_needed' => 15000.00, 'amount_found' => 10000.00, 'description' => 'Budget mise en œuvre'],
                ['resource_type' => 'matériel', 'amount_needed' => 8000.00, 'amount_found' => 4000.00, 'description' => 'Équipement nécessaire'],
            ],
            2 => [
                ['resource_type' => 'budget', 'amount_needed' => 3000.00, 'amount_found' => 2000.00, 'description' => 'Budget évaluation'],
            ],
        ];

        foreach ($phases as $phase) {
            $phaseOrder = $phase->order - 1;

            if (isset($resourcesByPhase[$phaseOrder])) {
                foreach ($resourcesByPhase[$phaseOrder] as $resourceData) {
                    $phase->resources()->create($resourceData);
                }
            }
        }
    }
}
