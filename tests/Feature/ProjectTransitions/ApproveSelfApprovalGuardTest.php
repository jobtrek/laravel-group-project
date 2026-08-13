<?php

use App\Enums\Role;
use App\Models\Project;
use App\Models\States\ArchiveState;
use App\Models\States\EvaluationState;
use App\Models\States\RecolteState;
use App\Models\States\RevisionState;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

beforeEach(function () {
    Mail::fake();

    $this->seed(RoleAndPermissionSeeder::class);
});

it('forbids a direction user from approving their own project', function () {

    $direction = User::factory()->create();
    $direction->assignRole(Role::Direction->value);

    $project = Project::factory()->evaluation()->create(['proposer_id' => $direction->id]);

    $response = $this->actingAs($direction)->patch(route('projects.approve', $project));

    $response->assertForbidden();
    expect($project->fresh()->status)->toBeInstanceOf(EvaluationState::class);
});

it('allows a direction user to approve a project they did not propose', function () {

    $direction = User::factory()->create();
    $direction->assignRole(Role::Direction->value);

    $proposer = User::factory()->create();
    $project = Project::factory()->evaluation()->create(['proposer_id' => $proposer->id]);

    $response = $this->actingAs($direction)->patch(route('projects.approve', $project));

    $response->assertRedirect();
    expect($project->fresh()->status)->toBeInstanceOf(RecolteState::class);
});

it('allows an admin with manage everything permission to approve their own project', function () {

    $admin = User::factory()->create();
    $admin->assignRole(Role::Admin->value);

    $project = Project::factory()->evaluation()->create(['proposer_id' => $admin->id]);

    $response = $this->actingAs($admin)->patch(route('projects.approve', $project));

    $response->assertRedirect();
    expect($project->fresh()->status)->toBeInstanceOf(RecolteState::class);
});

it('forbids a direction user from denying their own project', function () {

    $direction = User::factory()->create();
    $direction->assignRole(Role::Direction->value);

    $project = Project::factory()->evaluation()->create(['proposer_id' => $direction->id]);

    $response = $this->actingAs($direction)->patch(route('projects.deny', $project));

    $response->assertForbidden();
    expect($project->fresh()->status)->toBeInstanceOf(EvaluationState::class);
});

it('allows a direction user to deny a project they did not propose', function () {

    $direction = User::factory()->create();
    $direction->assignRole(Role::Direction->value);

    $proposer = User::factory()->create();
    $project = Project::factory()->evaluation()->create(['proposer_id' => $proposer->id]);

    $response = $this->actingAs($direction)->patch(route('projects.deny', $project));

    $response->assertRedirect();
    expect($project->fresh()->status)->toBeInstanceOf(ArchiveState::class);
});

it('allows an admin with manage everything permission to deny their own project', function () {

    $admin = User::factory()->create();
    $admin->assignRole(Role::Admin->value);

    $project = Project::factory()->evaluation()->create(['proposer_id' => $admin->id]);

    $response = $this->actingAs($admin)->patch(route('projects.deny', $project));

    $response->assertRedirect();
    expect($project->fresh()->status)->toBeInstanceOf(ArchiveState::class);
});

it('forbids a direction user from requesting more info on their own project', function () {

    $direction = User::factory()->create();
    $direction->assignRole(Role::Direction->value);

    $project = Project::factory()->evaluation()->create(['proposer_id' => $direction->id]);

    $response = $this->actingAs($direction)->post(route('projects.request-more-info', $project), [
        'field_comments' => ['title' => 'Please clarify this.'],
    ]);

    $response->assertForbidden();
    expect($project->fresh()->status)->toBeInstanceOf(EvaluationState::class);
});

it('allows a direction user to request more info on a project they did not propose', function () {

    $direction = User::factory()->create();
    $direction->assignRole(Role::Direction->value);

    $proposer = User::factory()->create();
    $project = Project::factory()->evaluation()->create(['proposer_id' => $proposer->id]);

    $response = $this->actingAs($direction)->post(route('projects.request-more-info', $project), [
        'field_comments' => ['title' => 'Please clarify this.'],
    ]);

    $response->assertRedirect();
    expect($project->fresh()->status)->toBeInstanceOf(RevisionState::class);
});
