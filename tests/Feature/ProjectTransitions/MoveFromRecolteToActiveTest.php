<?php

use App\Models\Project;
use App\Models\States\EncoursState;
use App\Models\States\RecolteState;
use App\Models\User;

it('refuses to transition when no chef de projet has been saved', function () {
    $user = User::factory()->create();
    $project = Project::factory()->recolte()->create(['leader_id' => null]);

    $response = $this->actingAs($user)->patch(route('projects.recolte.activate', $project));

    $response->assertSessionHas('error');

    expect($project->fresh()->status)->toBeInstanceOf(RecolteState::class);
});

it('transitions to Encours once a chef de projet has already been saved', function () {
    $user = User::factory()->create();
    $leader = User::factory()->create();
    $project = Project::factory()->recolte()->create(['leader_id' => $leader->id]);

    $response = $this->actingAs($user)->patch(route('projects.recolte.activate', $project));

    $response->assertRedirect(route('en-cours'));

    expect($project->fresh()->status)->toBeInstanceOf(EncoursState::class);
});
