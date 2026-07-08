<?php

namespace App\Models\States;

class PropositionState extends ProjectState
{
    public static string $name = 'proposition';

    public function label(): string
    {
        return 'Proposition';
    }

    public function color(): string
    {
        return 'inline-block rounded-full bg-cyan-100 px-3 py-1 text-xs font-medium text-indigo-700';
    }

    public function isEditable(): bool
    {
        return true;
    }
}
