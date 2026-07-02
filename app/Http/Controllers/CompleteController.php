<?php

namespace App\Http\Controllers;

use App\Enums\Stage;
use App\Models\States\CompleteState;

class CompleteController extends StageProjectController
{
    protected function stage(): Stage
    {
        return Stage::Complete;
    }

    protected function states(): string|array
    {
        return CompleteState::class;
    }
}
