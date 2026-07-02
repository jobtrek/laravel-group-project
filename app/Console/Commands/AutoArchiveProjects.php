<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Mail\ProjectArchivedMail;
use App\Models\Project;
use App\Models\States\EvaluationState;
use App\Models\States\PropositionState;
use App\Models\States\RecolteState;
use App\Models\States\RevisionState;
use App\Models\User;
use App\Service\ProjectService;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
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

        foreach ($projects as $project) {
            $this->archive($project);
            $this->notify($project, collect([$project->proposer]));
        }

        return $projects->count();
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

            if ($lastActivityAt->lt(now()->subMonths(12))) {
                $this->archive($project);
                $this->notify($project, $this->recolteRecipients($project));
                $archivedCount++;
            }
        }

        return $archivedCount;
    }

    private function archive(Project $project): void
    {
        ProjectService::archive($project);
    }

    /** @return Collection<int, User> */
    private function recolteRecipients(Project $project): Collection
    {
        return collect([$project->proposer, $project->recolteManager, $project->leader])
            ->merge($project->members)
            ->filter()
            ->unique('email');
    }

    /** @param Collection<int, User|null> $recipients */
    private function notify(Project $project, Collection $recipients): void
    {
        foreach ($recipients->filter()->unique('email') as $user) {
            Mail::to($user->email)->send(new ProjectArchivedMail($user, $project));
        }
    }
}
