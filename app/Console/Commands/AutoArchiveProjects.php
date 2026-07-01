<?php

namespace App\Console\Commands;

use App\Mail\RecolteArchivingMail;
use App\Models\Project;
use App\Models\States\ArchiveState;
use App\Models\States\EvaluationState;
use App\Models\States\PropositionState;
use App\Models\States\RecolteState;
use App\Models\States\RevisionState;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AutoArchiveProjects extends Command
{
    protected $signature = 'projects:auto-archive';

    protected $description = 'Archive inactive projects. Threshold depends on the project\'s current stage.';

    public function handle(): int
    {
        $archived = $this->archiveStalePropositionsAndEvaluations()
            + $this->archiveStaleRecolte();

        $this->info("Archived {$archived} project(s).");

        return self::SUCCESS;
    }

    private function archiveStalePropositionsAndEvaluations(): int
    {
        $projects = Project::whereState('status', [
            PropositionState::class,
            RevisionState::class,
            EvaluationState::class,
        ])
            ->where('updated_at', '<', now()->subMonths(3))
            ->get();

        foreach ($projects as $project) {
            $this->archive($project);
        }

        return $projects->count();
    }

    private function archiveStaleRecolte(): int
    {
        $projects = Project::whereState('status', RecolteState::class)
            ->withMax('resourceContributions as last_contribution_at', 'created_at')
            ->with(['proposer', 'recolteManager'])
            ->get();

        $archivedCount = 0;

        foreach ($projects as $project) {
            $lastActivityAt = $project->last_contribution_at ?? $project->updated_at;

            if ($lastActivityAt->lt(now()->subMonths(12))) {
                $this->archive($project);
                $this->sendRecolteArchivingEmails($project);
                $archivedCount++;
            }
        }

        return $archivedCount;
    }

    private function archive(Project $project): void
    {
        DB::transaction(function () use ($project): void {
            $project->current_stage = $project->getRawOriginal('status');
            $project->status->transitionTo(ArchiveState::class);
            $project->archived_at = now();
            $project->save();
        });
    }

    private function sendRecolteArchivingEmails(Project $project): void
    {
        $proposer = $project->proposer;
        $recolteManager = $project->recolteManager;

        if ($recolteManager && $proposer && $recolteManager->email !== $proposer->email) {
            Mail::to($recolteManager->email)->send(new RecolteArchivingMail($recolteManager, $project));
        }

        if ($proposer) {
            Mail::to($proposer->email)->send(new RecolteArchivingMail($proposer, $project));
        }
    }
}