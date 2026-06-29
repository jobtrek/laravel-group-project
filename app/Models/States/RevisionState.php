<?php

namespace App\Models\States;

use App\Models\States\ProjectState;

class RevisionState extends ProjectState
{
    public static string $name = 'révision';

    public function label(): string
    {
        return 'révision';
    }
}