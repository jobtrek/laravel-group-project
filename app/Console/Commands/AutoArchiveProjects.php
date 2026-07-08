<?php

namespace App\Console\Commands;

use App\Mail\ProjectArchivedMail;
use App\Models\Project;
use App\Models\States\ArchiveState;
use App\Models\States\EvaluationState;
use App\Models\States\PropositionState;
use App\Models\States\RecolteState;
use App\Models\States\RevisionState;
use App\Models\User;
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
            ->with('proposer')
            ->where('updated_at', '<', now()->subMonths(3))
            ->get();

        $archivedCount = 0;

        foreach ($projects as $project) {
            if ($archived = $this->archive($project)) {
                $this->notify($archived, collect([$archived->proposer])->filter()->values()->all());
                $archivedCount++;
            }
        }

        return $archivedCount;
    }

    private function archiveStaleRecolte(): int
    {
        $projects = Project::whereState('status', RecolteState::class)
            ->withMax('resourceContributions as last_contribution_at', 'created_at')
            ->withCasts(['last_contribution_at' => 'datetime'])
            ->with(['proposer', 'recolteManager', 'leader', 'members'])
            ->get();

        $archivedCount = 0;

        foreach ($projects as $project) {
            $lastActivityAt = $project->last_contribution_at ?? $project->updated_at;

            if (! $lastActivityAt->lt(now()->subMonths(12))) {
                continue;
            }

            if ($archived = $this->archive($project)) {
                $this->notify($archived, $this->recolteRecipients($archived));
                $archivedCount++;
            }
        }

        return $archivedCount;
    }

    private function archive(Project $project): ?Project
    {
        return DB::transaction(function () use ($project): ?Project {
            $locked = Project::whereKey($project->id)->lockForUpdate()->first();

            if (! $locked || $locked->status instanceof ArchiveState) {
                return null; 
            }

            $locked->setRelations($project->getRelations());
            $locked->current_stage = $locked->getRawOriginal('status');
            $locked->status->transitionTo(ArchiveState::class);
            $locked->archived_at = now();
            $locked->save();

            return $locked;
        });
    }

    /** @return array<int, User> */
    private function recolteRecipients(Project $project): array
    {
        return collect([$project->proposer, $project->recolteManager, $project->leader])
            ->merge($project->members)
            ->filter()
            ->unique('email')
            ->values()
            ->all();
    }

    /** @param array<int, User> $recipients */
    private function notify(Project $project, array $recipients): void
    {
        foreach ($recipients as $user) {
            Mail::to($user->email)->queue(new ProjectArchivedMail($user, $project));
        }
    }
}