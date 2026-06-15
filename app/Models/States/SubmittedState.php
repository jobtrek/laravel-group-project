<?php

namespace App\Models\States;

class SubmittedState extends ProjectState
{
    public static string $name = 'submitted';

    public function label(): string
    {
        return 'submitted';
    }
}
