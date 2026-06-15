<?php

namespace App\Models\States;

class DraftState extends ProjectState
{
    public static string $name = 'draft';

    public function label(): string
    {
        return 'draft';
    }
}
