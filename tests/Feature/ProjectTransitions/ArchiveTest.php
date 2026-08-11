<?php

use App\Mail\ProjectArchivedMail;
use App\Models\Project;
use App\Models\States\ArchiveState;
use App\Models\States\PropositionState;
use App\Service\ProjectService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

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

it('keeps AutoArchiveProjects archiving stale Recolte projects through the shared archive transition', function () {
    Mail::fake();

    Project::factory()->recolte()->create([
        'updated_at' => now()->subYears(2),
    ]);

    Artisan::call('projects:auto-archive');

    Mail::assertQueued(ProjectArchivedMail::class);

    it('keeps AutoArchiveProjects archiving stale En cours projects with no recent leader comment', function () {
    Mail::fake();

    $project = Project::factory()->enCours()->create([
        'updated_at' => now()->subMonths(4),
    ]);

    Artisan::call('projects:auto-archive');

    expect($project->fresh()->status)->toBeInstanceOf(ArchiveState::class);
    Mail::assertQueued(ProjectArchivedMail::class);
});

it('keeps AutoArchiveProjects archiving stale Complete projects through the shared archive transition', function () {
    Mail::fake();

    $leader = User::factory()->create();

    $project = Project::factory()->complete()->create([
        'leader_id' => $leader->id,
        'updated_at' => now()->subMonths(4),
    ]);

    Artisan::call('projects:auto-archive');

    expect($project->fresh()->status)->toBeInstanceOf(ArchiveState::class);
    Mail::assertQueued(ProjectArchivedMail::class);
});

it('permanently deletes Completed projects past their retention window', function () {
    $project = Project::factory()->complete()->create([
        'updated_at' => now()->subMonths(13),
    ]);

    Artisan::call('projects:auto-archive');

    expect(Project::find($project->id))->toBeNull();
});

it('permanently deletes Archived projects past their retention window', function () {
    $project = Project::factory()->archive()->create([
        'archived_at' => now()->subMonths(13),
    ]);

    Artisan::call('projects:auto-archive');

    expect(Project::find($project->id))->toBeNull();
});

});