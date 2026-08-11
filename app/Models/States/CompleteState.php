<?php

namespace App\Models\States;

class CompleteState extends ProjectState
{
    public static string $name = 'complété';

    public function label(): string
    {
        return 'Complété';
    }

    public function color(): string
    {
        return 'inline-block rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-700';
    }
}
