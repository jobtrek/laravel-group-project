<?php

namespace App\Service;

use App\Models\Project;
use App\Models\States\ApprovedState;
use App\Models\States\EvaluationState;
use App\Models\States\RefusedState;
use App\Models\States\SubmittedState;
use App\Models\User;

class ProjectService
{
    public static function getAllProjectsWithUsers(): array
    {
        $projects = Project::select('id', 'title', 'description')->with('evaluation')->get();
        $users = User::select('id', 'name')->get();

        return compact('projects', 'users');
    }

    public static function approve(Project $project): void
    {
        $project->status->transitionTo(ApprovedState::class);
        $project->save();
    }

    public static function deny(Project $project): void
    {
        $project->status->transitionTo(RefusedState::class);
        $project->save();
    }

    public static function requestMoreInfo(Project $project): void
    {
        $project->status->transitionTo(EvaluationState::class);
        $project->save();
    }

    public static function reSubmit(Project $project): void
    {
        $project->status->transitionTo(SubmittedState::class);
        $project->save();
    }
}
