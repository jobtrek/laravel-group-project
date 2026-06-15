<?php

namespace App\Models\States;

class ModificationState extends ProjectState
{
    public static string $name = 'modification';

    public function label(): string
    {
        return 'modification';
    }
}
