<?php

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role as PermissionRole;

uses(RefreshDatabase::class);

beforeEach(function () {
    Permission::firstOrCreate(['name' => 'manage everything', 'guard_name' => 'web']);
    PermissionRole::firstOrCreate(['name' => 'collaborateur', 'guard_name' => 'web']);
    PermissionRole::firstOrCreate(['name' => 'chef_de_projet', 'guard_name' => 'web']);
});

it('forbids a non-lead user from commenting on an in-progress project', function () {
    $user = User::factory()->create()->assignRole('collaborateur');
    $leader = User::factory()->create();
    $project = Project::factory()->enCours()->create(['leader_id' => $leader->id]);

    $response = $this->actingAs($user)->post(route('projects.comments.store', $project), [
        'content' => 'Not allowed',
        'stage' => 'en_cours',
    ]);

    $response->assertForbidden();
    expect($project->comments()->count())->toBe(0);
});
