<?php

namespace App\Models\States;

class CollectingState extends ProjectState
{
    public function label(): string
    {
        return 'collecting';
    }
}
