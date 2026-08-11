<?php

namespace App\Console\Commands;

use App\Enums\Role;
use App\Mail\ProjectArchivedMail;
use App\Mail\ProjectsPermanentlyDeletedMail;
use App\Models\Project;
use App\Models\States\ArchiveState;
use App\Models\States\CompleteState;
use App\Models\States\EvaluationState;
use App\Models\States\PropositionState;
use App\Models\States\RecolteState;
use App\Models\States\RevisionState;
use App\Models\User;
use App\Service\ProjectService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AutoArchiveProjects extends Command
{
    protected $signature = 'projects:auto-archive';

    protected $description = 'Archive inactive projects, and permanently delete projects past their retention window.';

    public function handle(): int
    {
        $archivedCount = $this->archiveStalePropositionsAndEvaluations()
            + $this->archiveStaleRecolte()
            + $this->archiveStaleEncours();

        $deletedProjects = array_merge(
            $this->deleteStaleCompletedProjects(),
            $this->deleteStaleArchivedProjects(),
        );

        if (! empty($deletedProjects)) {
            $this->notifyAdminsOfDeletions($deletedProjects);
        }

        $deletedCount = count($deletedProjects);

        $this->info("Archived {$archivedCount} project(s), permanently deleted {$deletedCount} project(s).");

        return self::SUCCESS;
    }

    private function archiveStalePropositionsAndEvaluations(): int
    {
        $staleAfterMonths = (int) config('projects.stale_after_months', 3);
        $archivedCount = 0;

        Project::whereState('status', [
            PropositionState::class,
            RevisionState::class,
            EvaluationState::class,
        ])
            ->with('proposer')
            ->where('updated_at', '<', now()->subMonths($staleAfterMonths))
            ->chunkById(100, function ($projects) use (&$archivedCount) {
                foreach ($projects as $project) {
                    if ($archived = $this->archive($project)) {
                        $this->notify($archived, array_filter([$archived->proposer]));
                        $archivedCount++;
                    }
                }
            });

        return $archivedCount;
    }

    private function archiveStaleRecolte(): int
    {
        $recolteArchiveAfterMonths = (int) config('projects.recolte_archive_after_months', 12);
        $archivedCount = 0;

        Project::whereState('status', RecolteState::class)
            ->withMax('resourceContributions as last_contribution_at', 'created_at')
            ->withCasts(['last_contribution_at' => 'datetime'])
            ->with(['proposer', 'recolteManager', 'leader', 'members'])
            ->chunkById(100, function ($projects) use (&$archivedCount, $recolteArchiveAfterMonths) {
                foreach ($projects as $project) {
                    $lastActivityAt = $project->last_contribution_at ?? $project->updated_at;

                    if (! $lastActivityAt->lt(now()->subMonths($recolteArchiveAfterMonths))) {
                        continue;
                    }

                    if ($archived = $this->archive($project)) {
                        $this->notify($archived, $this->recolteRecipients($archived));
                        $archivedCount++;
                    }
                }
            });

        return $archivedCount;
    }

    private function archiveStaleEncours(): int
    {
        $staleAfterMonths = (int) config('projects.stale_after_months', 3);
        $archivedCount = 0;

        Project::with(['leader', 'members'])
            ->needingProgressReminder()
            ->chunkById(100, function ($projects) use (&$archivedCount, $staleAfterMonths) {
                foreach ($projects as $project) {
                    $lastActivityAt = $project->last_leader_comment_at ?? $project->updated_at;

                    if (! $lastActivityAt->lt(now()->subMonths($staleAfterMonths))) {
                        continue;
                    }

                    if ($archived = $this->archive($project)) {
                        $this->notify($archived, $this->leaderAndMembers($archived));
                        $archivedCount++;
                    }
                }
            });

        return $archivedCount;
    }

    /** @return array<int, array{id: int, title: string, stage: string, reason: string}> */
    private function deleteStaleCompletedProjects(): array
    {
        $completedRetentionMonths = (int) config('projects.completed_retention_months', 12);
        $deleted = [];

        Project::whereState('status', CompleteState::class)
            ->where('updated_at', '<', now()->subMonths($completedRetentionMonths))
            ->chunkById(100, function ($projects) use (&$deleted) {
                foreach ($projects as $project) {
                    if ($snapshot = $this->deleteIfStillEligible($project, CompleteState::class, 'completed_retention')) {
                        $deleted[] = $snapshot;
                    }
                }
            });

        return $deleted;
    }

    /** @return array<int, array{id: int, title: string, stage: string, reason: string}> */
    private function deleteStaleArchivedProjects(): array
    {
        $archiveRetentionMonths = (int) config('projects.archive_retention_months', 12);
        $deleted = [];

        Project::whereState('status', ArchiveState::class)
            ->whereNotNull('archived_at')
            ->where('archived_at', '<', now()->subMonths($archiveRetentionMonths))
            ->chunkById(100, function ($projects) use (&$deleted) {
                foreach ($projects as $project) {
                    if ($snapshot = $this->deleteIfStillEligible($project, ArchiveState::class, 'archive_retention')) {
                        $deleted[] = $snapshot;
                    }
                }
            });

        return $deleted;
    }

    /** @return array{id: int, title: string, stage: string, reason: string}|null */
    private function deleteIfStillEligible(Project $project, string $expectedStateClass, string $reason): ?array
    {
        return DB::transaction(function () use ($project, $expectedStateClass, $reason): ?array {
            $locked = Project::whereKey($project->id)->lockForUpdate()->first();

            if (! $locked || ! $locked->status instanceof $expectedStateClass) {
                return null; // Restored, or state changed by another process — don't delete.
            }

            $snapshot = [
                'id' => $locked->id,
                'title' => $locked->title,
                'stage' => $locked->getRawOriginal('status'),
                'reason' => $reason,
            ];

            $locked->delete();

            Log::info('Project permanently deleted by retention policy.', $snapshot);

            return $snapshot;
        });
    }

    /** @param array<int, array{id: int, title: string, stage: string, reason: string}> $deletedProjects */
    private function notifyAdminsOfDeletions(array $deletedProjects): void
    {
        $admins = User::role(Role::Admin->value)->get();

        foreach ($admins as $admin) {
            Mail::to($admin->email)->queue(new ProjectsPermanentlyDeletedMail($admin, $deletedProjects));
        }
    }

    private function archive(Project $project): ?Project
    {
        return DB::transaction(function () use ($project): ?Project {
            $locked = Project::whereKey($project->id)->lockForUpdate()->first();

            if (! $locked || $locked->status instanceof ArchiveState) {
                return null; // Already archived by a concurrent run — skip silently.
            }

            $locked->setRelations($project->getRelations());
            ProjectService::archive($locked);

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

    /** @return array<int, User> */
    private function leaderAndMembers(Project $project): array
    {
        return collect([$project->leader])
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