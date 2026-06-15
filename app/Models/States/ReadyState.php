<?php

namespace App\Models\States;

class ReadyState extends ProjectState
{
    public function label(): string
    {
        return 'ready';
    }
}
