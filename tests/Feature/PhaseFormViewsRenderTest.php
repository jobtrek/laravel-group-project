<?php

use App\Models\Comment;
use App\Models\PhaseResource;
use App\Models\Project;
use App\Models\ProjectPhase;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;

beforeEach(fn () => $this->seed(RoleAndPermissionSeeder::class));

it('renders the edit view without errors', function () {
    $user = User::factory()->create()->assignRole('collaborateur');
    $project = Project::factory()->proposition()->has(
        ProjectPhase::factory()->has(PhaseResource::factory()->count(2), 'resources')->count(2),
        'phases'
    )->create(['proposer_id' => $user->id]);

    $this->actingAs($user)->get(route('projects.edit', $project))->assertOk();
});

it('renders the revision-form view without errors', function () {
    $user = User::factory()->create()->assignRole('collaborateur');
    $project = Project::factory()->revision()->has(
        ProjectPhase::factory()->has(PhaseResource::factory()->count(2), 'resources')->count(2),
        'phases'
    )->create(['proposer_id' => $user->id]);

    Comment::create([
        'content' => 'Fix this',
        'field_key' => 'phases.0.objectifs',
        'stage' => 'review',
        'user_id' => $user->id,
        'project_id' => $project->id,
    ]);
    Comment::create([
        'content' => 'Fix that',
        'field_key' => 'phases.0.livrables',
        'stage' => 'review',
        'user_id' => $user->id,
        'project_id' => $project->id,
    ]);
    Comment::create([
        'content' => 'Fix resources',
        'field_key' => 'phases.0.ressources',
        'stage' => 'review',
        'user_id' => $user->id,
        'project_id' => $project->id,
    ]);
    Comment::create([
        'content' => 'Fix but',
        'field_key' => 'but',
        'stage' => 'review',
        'user_id' => $user->id,
        'project_id' => $project->id,
    ]);

    $this->actingAs($user)->get(route('projects.revision-form', $project))->assertOk();
});

it('renders the proposition wizard (create) view without errors', function () {
    $user = User::factory()->create()->assignRole('collaborateur');

    $this->actingAs($user)->get(route('create'))->assertOk();
});
