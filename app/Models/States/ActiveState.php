<?php

namespace App\Models\States;

class ActiveState extends ProjectState
{
    public function label(): string
    {
        return 'active';
    }
}
