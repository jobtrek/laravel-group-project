<?php

namespace App\Http\Controllers;

use App\Enums\Stage;
use App\Models\States\ArchiveState;
use App\Models\States\CompleteState;

class ArchiveController extends StageProjectController
{
    protected function stage(): Stage
    {
        return Stage::Archive;
    }

    protected function states(): string|array
    {
        return [ArchiveState::class, CompleteState::class];
    }
}
