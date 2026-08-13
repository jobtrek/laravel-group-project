<?php

use App\Enums\Role;
use App\Models\Project;
use App\Models\States\PropositionState;
use App\Models\States\RecolteState;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

beforeEach(function () {
    Mail::fake();

    $this->seed(RoleAndPermissionSeeder::class);
});

it('forbids approving a project that is not in evaluation', function () {
    $direction = User::factory()->create();
    $direction->assignRole(Role::Direction->value);

    $project = Project::factory()->recolte()->create();

    $response = $this->actingAs($direction)->patch(route('projects.approve', $project));

    $response->assertForbidden();
    expect($project->fresh()->status)->toBeInstanceOf(RecolteState::class);
});

it('forbids sending a project to direction when it is not a proposition', function () {
    $direction = User::factory()->create();
    $direction->assignRole(Role::Direction->value);

    $project = Project::factory()->recolte()->create();

    $response = $this->actingAs($direction)->patch(route('projects.send-to-direction', $project));

    $response->assertForbidden();
    expect($project->fresh()->status)->toBeInstanceOf(RecolteState::class);
});

it('allows sending a proposition project to direction', function () {
    $direction = User::factory()->create();
    $direction->assignRole(Role::Direction->value);

    $project = Project::factory()->proposition()->create();

    $response = $this->actingAs($direction)->patch(route('projects.send-to-direction', $project));

    $response->assertRedirect();
    expect($project->fresh()->status)->not->toBeInstanceOf(PropositionState::class);
});
