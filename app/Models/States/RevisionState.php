<?php

namespace App\Models\States;

class RevisionState extends ProjectState
{
    public static string $name = 'révision';

    public function label(): string
    {
        return 'Révision';
    }

    public function color(): string
    {
        return 'inline-block rounded-full bg-orange-100 px-3 py-1 text-xs font-medium text-orange-700';
    }
}
