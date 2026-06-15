<?php

namespace App\Models\States;

class RefusedState extends ProjectState
{
    public static string $name = 'refused';

    public function label(): string
    {
        return 'refused';
    }
}
