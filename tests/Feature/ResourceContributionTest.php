<?php

use App\Models\PhaseResource;
use App\Models\Project;
use App\Models\ProjectPhase;
use App\Models\ResourceContribution;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;

beforeEach(fn () => $this->seed(RoleAndPermissionSeeder::class));

it('shows only the resource types defined on the project phases', function () {
    $user = User::factory()->create()->assignRole('recolte_manager');
    $project = Project::factory()->recolte()->create();
    $phase = ProjectPhase::factory()->create(['project_id' => $project->id]);
    PhaseResource::factory()->create(['phase_id' => $phase->id, 'resource_type' => 'Budget', 'amount_needed' => 1000]);
    PhaseResource::factory()->create(['phase_id' => $phase->id, 'resource_type' => 'Volunteers', 'amount_needed' => 50]);

    $response = $this->actingAs($user)->get(route('projects.resources.create', $project));

    $response->assertOk();
    $response->assertSee('Budget');
    $response->assertSee('Volunteers');
});

it('records a contribution scoped to a specific resource type', function () {
    $user = User::factory()->create()->assignRole('recolte_manager');
    $project = Project::factory()->recolte()->create();
    $phase = ProjectPhase::factory()->create(['project_id' => $project->id]);
    PhaseResource::factory()->create(['phase_id' => $phase->id, 'resource_type' => 'Budget', 'amount_needed' => 1000]);

    $response = $this->actingAs($user)->post(route('projects.resources.store', $project), [
        'phase_id' => $phase->id,
        'resource_type' => 'Budget',
        'description' => 'Don initial',
        'amount' => 400,
    ]);

    $response->assertRedirect(route('recolte'));

    expect(ResourceContribution::where('resource_type', 'Budget')->sum('amount'))->toEqual('400.00');
});

it('rejects a resource type that is not defined on the selected phase', function () {
    $user = User::factory()->create()->assignRole('recolte_manager');
    $project = Project::factory()->recolte()->create();
    $phase = ProjectPhase::factory()->create(['project_id' => $project->id]);
    PhaseResource::factory()->create(['phase_id' => $phase->id, 'resource_type' => 'Budget', 'amount_needed' => 1000]);

    $response = $this->actingAs($user)->post(route('projects.resources.store', $project), [
        'phase_id' => $phase->id,
        'resource_type' => 'Bénévoles',
        'amount' => 10,
    ]);

    $response->assertSessionHasErrors('resource_type');
    expect(ResourceContribution::count())->toBe(0);
});

it('rejects a contribution that exceeds what is still needed for that specific resource type', function () {
    $user = User::factory()->create()->assignRole('recolte_manager');
    $project = Project::factory()->recolte()->create();
    $phase = ProjectPhase::factory()->create(['project_id' => $project->id]);
    PhaseResource::factory()->create(['phase_id' => $phase->id, 'resource_type' => 'Budget', 'amount_needed' => 100]);
    PhaseResource::factory()->create(['phase_id' => $phase->id, 'resource_type' => 'Bénévoles', 'amount_needed' => 1000]);

    ResourceContribution::create([
        'phase_id' => $phase->id,
        'user_id' => $user->id,
        'resource_type' => 'Budget',
        'amount' => 80,
    ]);

    // Even though the phase overall still needs plenty (Bénévoles untouched),
    // Budget itself only has 20 remaining, so this must be rejected.
    $response = $this->actingAs($user)->post(route('projects.resources.store', $project), [
        'phase_id' => $phase->id,
        'resource_type' => 'Budget',
        'amount' => 50,
    ]);

    $response->assertSessionHasErrors('amount');
});

it('rejects a contribution amount with more than two decimals', function () {
    $user = User::factory()->create()->assignRole('recolte_manager');
    $project = Project::factory()->recolte()->create();
    $phase = ProjectPhase::factory()->create(['project_id' => $project->id]);
    PhaseResource::factory()->create(['phase_id' => $phase->id, 'resource_type' => 'Budget', 'amount_needed' => 100]);

    $response = $this->actingAs($user)->post(route('projects.resources.store', $project), [
        'phase_id' => $phase->id,
        'resource_type' => 'Budget',
        'amount' => 10.015,
    ]);

    $response->assertSessionHasErrors('amount');
    expect(ResourceContribution::count())->toBe(0);
});

it('caps a contribution at the configured over-funding multiplier', function () {
    config(['projects.resource_overfunding_multiplier' => 1.5]);

    $user = User::factory()->create()->assignRole('recolte_manager');
    $project = Project::factory()->recolte()->create();
    $phase = ProjectPhase::factory()->create(['project_id' => $project->id]);
    PhaseResource::factory()->create(['phase_id' => $phase->id, 'resource_type' => 'Budget', 'amount_needed' => 100]);

    // 150 is exactly the cap, 150.01 is over it.
    $this->actingAs($user)->post(route('projects.resources.store', $project), [
        'phase_id' => $phase->id,
        'resource_type' => 'Budget',
        'amount' => 150,
    ])->assertRedirect(route('recolte'));

    $this->actingAs($user)->post(route('projects.resources.store', $project), [
        'phase_id' => $phase->id,
        'resource_type' => 'Budget',
        'amount' => 0.01,
    ])->assertSessionHasErrors('amount');

    expect(ResourceContribution::sum('amount'))->toEqual('150.00');
});

it('rejects a contribution on an archived project', function () {
    $user = User::factory()->create()->assignRole('recolte_manager');
    $project = Project::factory()->archive()->create();

    $response = $this->actingAs($user)->post(route('projects.resources.store', $project), [
        'phase_id' => 1,
        'resource_type' => 'Budget',
        'amount' => 100,
    ]);

    $response->assertNotFound();
    expect(ResourceContribution::count())->toBe(0);
});
