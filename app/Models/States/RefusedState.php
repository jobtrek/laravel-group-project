<?php

namespace App\Models\States;

class RefusedState extends ProjectState
{
    public function label(): string
    {
        return 'refused';
    }
}
