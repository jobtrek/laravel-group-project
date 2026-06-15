<?php

namespace App\Models\States;

class DraftState extends ProjectState
{
    public function label(): string
    {
        return 'draft';
    }
}
