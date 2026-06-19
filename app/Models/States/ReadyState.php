<?php

namespace App\Models\States;

class ReadyState extends ProjectState
{
    public static string $name = 'ready';

    public function label(): string
    {
        return 'ready';
    }
}
