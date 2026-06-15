<?php

namespace App\Models\States;

class ArchivedState extends ProjectState
{
    public function label(): string
    {
        return 'archived';
    }
}
