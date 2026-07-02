<?php

use App\Models\Project;
use App\Models\States\RecolteState;
use App\Models\User;

it('refuses to save when no chef de projet is selected', function () {
    $user = User::factory()->create();
    $project = Project::factory()->recolte()->create(['leader_id' => null]);

    $response = $this->actingAs($user)->patch(route('projects.recolte.team', $project), [
        'leader_id' => '',
        'membres' => [''],
    ]);

    $response->assertSessionHasErrorsIn('assignTeam', ['leader_id']);

    expect($project->fresh()->leader_id)->toBeNull()
        ->and($project->fresh()->status)->toBeInstanceOf(RecolteState::class);
});

it('saves the chef de projet and members without transitioning the project', function () {
    $user = User::factory()->create();
    $leader = User::factory()->create();
    $member = User::factory()->create();
    $project = Project::factory()->recolte()->create(['leader_id' => null]);

    $response = $this->actingAs($user)->patch(route('projects.recolte.team', $project), [
        'leader_id' => $leader->id,
        'membres' => [$member->id],
    ]);

    $response->assertRedirect();

    $project->refresh();

    expect($project->leader_id)->toBe($leader->id)
        ->and($project->members->pluck('id')->all())->toBe([$member->id])
        ->and($project->status)->toBeInstanceOf(RecolteState::class);
});
