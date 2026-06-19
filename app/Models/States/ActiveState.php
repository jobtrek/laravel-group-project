<?php

namespace App\Models\States;

class ActiveState extends ProjectState
{
    public static string $name = 'active';

    public function label(): string
    {
        return 'active';
    }
}
