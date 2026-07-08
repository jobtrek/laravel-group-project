<?php

namespace App\Models\States;

class ArchiveState extends ProjectState
{
    public static string $name = 'archivé';

    public function label(): string
    {
        return 'Archivé';
    }

    public function color(): string
    {
        return 'inline-block rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600';
    }
}
