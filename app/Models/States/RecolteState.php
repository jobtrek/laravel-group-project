<?php

namespace App\Models\States;

class RecolteState extends ProjectState
{
    public static string $name = 'récolte';

    public function label(): string
    {
        return 'Récolte';
    }

    public function color(): string
    {
        return 'inline-block rounded-full bg-yellow-100 px-3 py-1 text-xs font-medium text-yellow-700';
    }
}
