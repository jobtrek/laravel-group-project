<?php

use App\Mail\ProjectArchivedMail;
use App\Models\Project;
use App\Models\States\ArchiveState;
use App\Models\States\PropositionState;
use App\Service\ProjectService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;

it('archives a project, snapshotting its prior stage and setting archived_at', function () {
    $project = Project::factory()->proposition()->create();

    ProjectService::archive($project);

    expect($project->status)->toBeInstanceOf(ArchiveState::class)
        ->and($project->current_stage)->toBe(PropositionState::getMorphClass())
        ->and($project->archived_at)->not->toBeNull();
});

it('keeps AutoArchiveProjects firing its own ProjectArchivedMail through the shared archive transition', function () {
    Mail::fake();

    Project::factory()->proposition()->create([
        'updated_at' => now()->subMonths(4),
    ]);

    Artisan::call('projects:auto-archive');

    Mail::assertQueued(ProjectArchivedMail::class);
});
