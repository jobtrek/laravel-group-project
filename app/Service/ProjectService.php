<?php

namespace App\Service;

use App\Mail\ApprovedEmail;
use App\Mail\DeniedEmail;
use App\Models\Project;
use App\Models\States\ArchiveState;
use App\Models\States\CompleteState;
use App\Models\States\EncoursState;
use App\Models\States\EvaluationState;
use App\Models\States\PropositionState;
use App\Models\States\RecolteState;
use App\Models\States\RevisionState;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ProjectService
{
    public static function getAllProjectsWithUsers(): array
    {
        $projects = Project::select('id', 'title', 'description')->with('evaluation')->get();
        $users = User::select('id', 'name')->get();

        return compact('projects', 'users');
    }

    public static function review(Project $project): void
    {
        $project->status->transitionTo(EvaluationState::class);
    }

    public static function approve(Project $project): void
    {
        $project->status->transitionTo(RecolteState::class);

        if ($proposer = $project->proposer) {
            Mail::to($proposer->email)->queue(new ApprovedEmail($proposer->name));
        }
    }

    public static function deny(Project $project): void
    {
        $project->status->transitionTo(ArchiveState::class);

        if ($proposer = $project->proposer) {
            Mail::to($proposer->email)->queue(new DeniedEmail($proposer->name));
        }
    }

    public static function requestMoreInfoTransition(Project $project): void
    {
        $project->status->transitionTo(RevisionState::class);
    }

    public static function reSubmit(Project $project): void
    {
        $project->status->transitionTo(PropositionState::class);
    }

    public static function complete(Project $project): void
    {
        $project->status->transitionTo(CompleteState::class);
    }

    public static function moveToEncours(Project $project): bool
    {
        if (! $project->status instanceof RecolteState) {
            return false;
        }

        if ($project->leader_id === null) {
            return false;
        }

        $project->status->transitionTo(EncoursState::class);

        return true;
    }

    public static function archive(Project $project): void
    {
        DB::transaction(function () use ($project): void {
            $project->current_stage = $project->getRawOriginal('status');
            $project->archived_at = now();
            // Persist snapshot metadata before status transition so this does not rely on transitionTo() side effects.
            $project->save();
            $project->status->transitionTo(ArchiveState::class);
        });
    }
}
