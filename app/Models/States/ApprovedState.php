<?php

namespace App\Models\States;

class ApprovedState extends ProjectState
{
    public function label(): string
    {
        return 'approved';
    }
}
