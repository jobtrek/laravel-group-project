<?php

use App\Models\Project;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;

uses(RefreshDatabase::class);


beforeEach(fn () => $this->seed(RoleAndPermissionSeeder::class));

function validPropositionPayload(array $overrides = []): array
{
    return array_merge([
        'titre' => 'Un projet de test',
        'description' => 'Une description valide.',
        'buts' => ['Objectif 1'],
        'perimetre' => 'Le périmètre du projet.',
        'phases' => [
            [
                'titre' => 'Phase 1',
                'duree' => '2 semaines',
                'description' => 'Description de la phase.',
                'objectifs' => ['Objectif de phase'],
                'livrables' => ['Livrable 1'],
                'ressources_necessaires' => [
                    ['resource_type' => 'Budget', 'amount_needed' => 100],
                ],
            ],
        ],
        'portee' => 25,
        'impact' => 3,
        'confiance' => 50,
        'effort' => 3,
    ], $overrides);
}

it('accepts a proposition submitted directly with valid data', function () {
    $user = User::factory()->create()->assignRole('collaborateur');

    $response = $this->actingAs($user)->post(route('proposition.store'), validPropositionPayload());

    $response->assertRedirect(route('dashboard'));
    expect(Project::count())->toBe(1);
});

it('rejects a proposition missing the perimetre field, bypassing the wizard', function () {
    $user = User::factory()->create()->assignRole('collaborateur');

    $payload = validPropositionPayload(['perimetre' => '']);

    $response = $this->actingAs($user)->post(route('proposition.store'), $payload);

    $response->assertSessionHasErrors('perimetre');
    expect(Project::count())->toBe(0);
});

it('rejects a proposition with an out-of-range portee value, bypassing the wizard', function () {
    $user = User::factory()->create()->assignRole('collaborateur');

    $payload = validPropositionPayload(['portee' => 51]);

    $response = $this->actingAs($user)->post(route('proposition.store'), $payload);

    $response->assertSessionHasErrors('portee');
    expect(Project::count())->toBe(0);
});

it('rejects a proposition with an out-of-range confiance value, bypassing the wizard', function () {
    $user = User::factory()->create()->assignRole('collaborateur');

    $payload = validPropositionPayload(['confiance' => 101]);

    $response = $this->actingAs($user)->post(route('proposition.store'), $payload);

    $response->assertSessionHasErrors('confiance');
    expect(Project::count())->toBe(0);
});

it('rejects a proposition missing required fields entirely, bypassing the wizard', function () {
    $user = User::factory()->create()->assignRole('collaborateur');

    $response = $this->actingAs($user)->post(route('proposition.store'), []);

    $response->assertSessionHasErrors([
        'titre', 'description', 'buts', 'perimetre', 'phases', 'portee', 'impact', 'confiance', 'effort',
    ]);
    expect(Project::count())->toBe(0);
});
