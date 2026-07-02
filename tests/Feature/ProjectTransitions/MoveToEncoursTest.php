<?php

use App\Models\Project;
use App\Models\ProjectPhase;
use App\Models\States\EncoursState;
use App\Models\States\RecolteState;
use App\Models\User;
use App\Service\ProjectService;

function projectWithProgress(float $needed, float $found): Project
{
    $project = Project::factory()->recolte()->create();
    $contributor = User::factory()->create();

    $phase = ProjectPhase::factory()->create(['project_id' => $project->id]);
    $phase->resources()->create([
        'resource_type' => 'money',
        'amount_needed' => $needed,
    ]);
    $phase->contributions()->create([
        'user_id' => $contributor->id,
        'resource_type' => 'money',
        'amount' => $found,
    ]);

    return $project->fresh(['phases.resources', 'phases.contributions']);
}

it('transitions a Recolte project to Encours once progress reaches 80%', function () {
    $project = projectWithProgress(1000, 800);

    $moved = ProjectService::moveToEncours($project);

    expect($moved)->toBeTrue()
        ->and($project->status)->toBeInstanceOf(EncoursState::class);
});

it('refuses to transition when progress is below 80%', function () {
    $project = projectWithProgress(1000, 799);

    $moved = ProjectService::moveToEncours($project);

    expect($moved)->toBeFalse()
        ->and($project->status)->toBeInstanceOf(RecolteState::class);
});

it('refuses to transition when the project is not in Recolte state', function () {
    $project = Project::factory()->enCours()->create();

    $moved = ProjectService::moveToEncours($project);

    expect($moved)->toBeFalse();
});
