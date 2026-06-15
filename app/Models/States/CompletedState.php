<?php

namespace App\Models\States;

class CompletedState extends ProjectState
{
    public function label(): string
    {
        return 'completed';
    }
}
