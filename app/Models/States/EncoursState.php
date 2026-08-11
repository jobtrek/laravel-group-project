<?php

namespace App\Models\States;

class EncoursState extends ProjectState
{
    public static string $name = 'en cours';

    public function label(): string
    {
        return 'En cours';
    }

    public function color(): string
    {
        return 'inline-block rounded-full bg-yellow-100 px-3 py-1 text-xs font-medium text-yellow-700';
    }
}
