<?php

namespace App\Models\States;

class EncoursState extends ProjectState
{
    public static string $name = 'En cours';

    public function label(): string
    {
        return 'En cours';
    }
}
