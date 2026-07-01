<?php

namespace App\Http\Controllers;

use App\Enums\Stage;
use App\Models\Project;
use App\Models\States\EvaluationState;
use Illuminate\View\View;

class ReviewController extends StageProjectController
{
    protected function stage(): Stage
    {
        return Stage::Evaluation;
    }

    protected function states(): string|array
    {
        return [EvaluationState::class];
    }

    public function showForm(Project $project): View
    {
        $project->load(['proposer', 'evaluation', 'phases.resources', 'members']);

        return view('review-form', compact('project'));
    }
}
