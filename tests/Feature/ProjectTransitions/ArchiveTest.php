<?php

use App\Enums\Role;
use App\Mail\ProjectArchivedMail;
use App\Mail\ProjectsPermanentlyDeletedMail;
use App\Models\Project;
use App\Models\States\ArchiveState;
use App\Models\States\PropositionState;
use App\Models\User;
use App\Service\ProjectService;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;

it('archives a project, snapshotting its prior stage and setting archived_at', function () {
    $project = Project::factory()->proposition()->create();

    ProjectService::archive($project);
    $archivedProject = $project->fresh();

    expect($archivedProject->status)->toBeInstanceOf(ArchiveState::class)
        ->and($archivedProject->current_stage)->toBe(PropositionState::getMorphClass())
        ->and($archivedProject->archived_at)->not->toBeNull();
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
});

it('keeps AutoArchiveProjects archiving stale En cours projects with no recent leader comment', function () {
    Mail::fake();

    $project = Project::factory()->enCours()->create([
        'updated_at' => now()->subMonths(4),
    ]);

    Artisan::call('projects:auto-archive');

    expect($project->fresh()->status)->toBeInstanceOf(ArchiveState::class);
    Mail::assertQueued(ProjectArchivedMail::class);
});

it('permanently deletes Completed projects past their retention window and notifies admins', function () {
    Mail::fake();
    $this->seed(RoleAndPermissionSeeder::class);
    $admin = User::factory()->create();
    $admin->assignRole(Role::Admin->value);

    $project = Project::factory()->complete()->create([
        'updated_at' => now()->subMonths(13),
    ]);

    Artisan::call('projects:auto-archive');

    expect(Project::find($project->id))->toBeNull();
    Mail::assertQueued(ProjectsPermanentlyDeletedMail::class);
});

it('permanently deletes Archived projects past their retention window and notifies admins', function () {
    Mail::fake();
    $this->seed(RoleAndPermissionSeeder::class);
    $admin = User::factory()->create();
    $admin->assignRole(Role::Admin->value);

    $project = Project::factory()->archive()->create([
        'archived_at' => now()->subMonths(13),
    ]);

    Artisan::call('projects:auto-archive');

    expect(Project::find($project->id))->toBeNull();
    Mail::assertQueued(ProjectsPermanentlyDeletedMail::class);
});
