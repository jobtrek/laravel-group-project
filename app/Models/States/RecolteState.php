<?php

namespace App\Models\States;

class RecolteState extends ProjectState
{
    public static string $name = 'récolte';

    public function label(): string
    {
        return 'récolte';
    }
}
