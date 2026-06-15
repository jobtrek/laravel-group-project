<?php

namespace App\Models\States;

class CompletedState extends ProjectState
{
    public static string $name = 'completed';

    public function label(): string
    {
        return 'completed';
    }
}
