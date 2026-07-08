<?php

use App\Models\Project;
use App\Models\States\EncoursState;
use App\Models\States\RecolteState;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;

beforeEach(fn () => $this->seed(RoleAndPermissionSeeder::class));

it('refuses to transition when no chef de projet has been saved', function () {
    $user = User::factory()->create()->assignRole('chef_de_projet');
    $project = Project::factory()->recolte()->create(['leader_id' => null]);

    $response = $this->actingAs($user)->patch(route('projects.recolte.activate', $project));

    $response->assertSessionHas('error');

    expect($project->fresh()->status)->toBeInstanceOf(RecolteState::class);
});

it('transitions to Encours once a chef de projet has already been saved', function () {
    $user = User::factory()->create()->assignRole('chef_de_projet');
    $project = Project::factory()->recolte()->create(['leader_id' => $user->id]);

    $response = $this->actingAs($user)->patch(route('projects.recolte.activate', $project));

    $response->assertRedirect(route('en-cours'));

    expect($project->fresh()->status)->toBeInstanceOf(EncoursState::class);
});
