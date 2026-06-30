<?php

namespace App\Models\States;

class RevisionState extends ProjectState
{
    public static string $name = 'révision';

    public function label(): string
    {
        return 'révision';
    }
}
