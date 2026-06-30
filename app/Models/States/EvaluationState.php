<?php

namespace App\Models\States;

class EvaluationState extends ProjectState
{
    public static string $name = 'évaluation';

    public function label(): string
    {
        return 'évaluation';
    }
}
