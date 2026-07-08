<?php

namespace App\Models\States;

class EvaluationState extends ProjectState
{
    public static string $name = 'évaluation';

    public function label(): string
    {
        return 'Évaluation';
    }

    public function color(): string
    {
        return 'inline-block rounded-full bg-cyan-100 px-3 py-1 text-xs font-medium text-indigo-700';
    }
}
