<?php

namespace App\Models\States;

class ArchiveState extends ProjectState
{
    public static string $name = 'archivé';

    public function label(): string
    {
        return 'archivé';
    }
}
