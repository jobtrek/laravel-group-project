<?php

use App\Models\Project;
use App\Models\States\EvaluationState;
use App\Models\States\RecolteState;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Permission;

it('forbids a direction user from approving their own project', function () {
    Mail::fake();

    Permission::firstOrCreate(['name' => 'approve', 'guard_name' => 'web']);
    Permission::firstOrCreate(['name' => 'manage everything', 'guard_name' => 'web']);

    $direction = User::factory()->create();
    $direction->givePermissionTo('approve');

    $project = Project::factory()->evaluation()->create(['proposer_id' => $direction->id]);

    $response = $this->actingAs($direction)->patch(route('projects.approve', $project));

    $response->assertForbidden();
    expect($project->fresh()->status)->toBeInstanceOf(EvaluationState::class);
});

it('allows a direction user to approve a project they did not propose', function () {
    Mail::fake();

    Permission::firstOrCreate(['name' => 'approve', 'guard_name' => 'web']);
    Permission::firstOrCreate(['name' => 'manage everything', 'guard_name' => 'web']);

    $direction = User::factory()->create();
    $direction->givePermissionTo('approve');

    $proposer = User::factory()->create();
    $project = Project::factory()->evaluation()->create(['proposer_id' => $proposer->id]);

    $response = $this->actingAs($direction)->patch(route('projects.approve', $project));

    $response->assertRedirect();
    expect($project->fresh()->status)->toBeInstanceOf(RecolteState::class);
});

it('allows an admin with manage everything permission to approve their own project', function () {
    Mail::fake();

    Permission::firstOrCreate(['name' => 'approve', 'guard_name' => 'web']);
    Permission::firstOrCreate(['name' => 'manage everything', 'guard_name' => 'web']);

    $admin = User::factory()->create();
    $admin->givePermissionTo(['approve', 'manage everything']);

    $project = Project::factory()->evaluation()->create(['proposer_id' => $admin->id]);

    $response = $this->actingAs($admin)->patch(route('projects.approve', $project));

    $response->assertRedirect();
    expect($project->fresh()->status)->toBeInstanceOf(RecolteState::class);
});
