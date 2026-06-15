<?php

namespace App\Models\States;

class ModificationState extends ProjectState
{
    public function label(): string
    {
        return 'modification';
    }
}
