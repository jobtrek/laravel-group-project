<?php

use App\Models\Project;
use App\Models\States\EncoursState;
use App\Models\States\RecolteState;
use App\Models\User;
use App\Service\ProjectService;

it('transitions a Recolte project to Encours once a project chief is assigned', function () {
    $leader = User::factory()->create();
    $project = Project::factory()->recolte()->create(['leader_id' => $leader->id]);

    $moved = ProjectService::moveToEncours($project);

    expect($moved)->toBeTrue()
        ->and($project->status)->toBeInstanceOf(EncoursState::class);
});

it('refuses to transition when no project chief is assigned', function () {
    $project = Project::factory()->recolte()->create(['leader_id' => null]);

    $moved = ProjectService::moveToEncours($project);

    expect($moved)->toBeFalse()
        ->and($project->status)->toBeInstanceOf(RecolteState::class);
});

it('refuses to transition when the project is not in Recolte state', function () {
    $leader = User::factory()->create();
    $project = Project::factory()->enCours()->create(['leader_id' => $leader->id]);

    $moved = ProjectService::moveToEncours($project);

    expect($moved)->toBeFalse();
});
