<?php

namespace App\Http\Controllers;

use App\Enums\Stage;
use App\Models\States\EncoursState;

class EnCoursController extends StageProjectController
{
    protected function stage(): Stage
    {
        return Stage::EnCours;
    }

    protected function states(): string|array
    {
        return EncoursState::class;
    }
}
