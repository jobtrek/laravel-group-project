<?php

namespace App\Models\States;

class CollectingState extends ProjectState
{
    public static string $name = 'collecting';

    public function label(): string
    {
        return 'collecting';
    }
}
