<?php

use App\Models\PhaseResource;
use App\Models\Project;
use App\Models\ProjectPhase;
use App\Models\ResourceContribution;
use App\Models\User;

it('shows only the resource types defined on the project phases', function () {
    $user = User::factory()->create();
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
    $user = User::factory()->create();
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
    $user = User::factory()->create();
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
    $user = User::factory()->create();
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
