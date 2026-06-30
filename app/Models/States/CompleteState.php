<?php

namespace App\Models\States;

class CompleteState extends ProjectState
{
    public static string $name = 'complété';

    public function label(): string
    {
        return 'complété';
    }
}
