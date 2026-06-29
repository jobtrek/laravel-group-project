<?php

namespace App\Models\States;

class PropositionState extends ProjectState
{
    public static string $name = 'proposition';

    public function label(): string
    {
        return 'proposition';
    }
}
