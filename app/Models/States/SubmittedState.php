<?php

namespace App\Models\States;

class SubmittedState extends ProjectState
{
    public function label(): string
    {
        return 'submitted';
    }
}
