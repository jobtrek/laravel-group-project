<?php

namespace App\Models\States;

class ArchivedState extends ProjectState
{
    public static string $name = 'archived';

    public function label(): string
    {
        return 'archived';
    }
}
