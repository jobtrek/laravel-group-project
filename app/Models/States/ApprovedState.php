<?php

namespace App\Models\States;

class ApprovedState extends ProjectState
{
    public static string $name = 'approved';

    public function label(): string
    {
        return 'approved';
    }
}
