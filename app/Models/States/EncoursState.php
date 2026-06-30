<?php

namespace App\Models\States;

class EncoursState extends ProjectState
{
    public static string $name = 'en cours';

    public function label(): string
    {
        return 'en cours';
    }
}
