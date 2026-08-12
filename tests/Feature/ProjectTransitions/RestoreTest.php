<?php

use App\Models\Project;
use App\Models\States\ArchiveState;
use App\Models\States\PropositionState;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;

beforeEach(fn () => $this->seed(RoleAndPermissionSeeder::class));

it('refuses to restore a project that is not archived', function () {
    $user = User::factory()->create()->assignRole('chef_de_projet');
    $project = Project::factory()->proposition()->create();

    $response = $this->actingAs($user)->patch(route('projects.restore', $project));

    $response->assertForbidden();

    expect($project->fresh()->status)->toBeInstanceOf(PropositionState::class);
});

it('denies restoring a project to a user without the restore permission', function () {
    $user = User::factory()->create()->assignRole('collaborateur');
    $project = Project::factory()->archive()->create();

    $response = $this->actingAs($user)->patch(route('projects.restore', $project));

    $response->assertForbidden();

    expect($project->fresh()->status)->toBeInstanceOf(ArchiveState::class);
});

it('restores an archived project to PropositionState and sets restored_at', function () {
    $user = User::factory()->create()->assignRole('chef_de_projet');
    $project = Project::factory()->archive()->create();

    $response = $this->actingAs($user)->patch(route('projects.restore', $project));

    $response->assertRedirect();

    $restoredProject = $project->fresh();

    expect($restoredProject->status)->toBeInstanceOf(PropositionState::class)
        ->and($restoredProject->restored_at)->not->toBeNull();
});
