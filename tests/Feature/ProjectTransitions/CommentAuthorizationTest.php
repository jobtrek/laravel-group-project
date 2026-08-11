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
    PermissionRole::firstOrCreate(['name' => 'direction', 'guard_name' => 'web']);
});
it('forbids a non-direction user from commenting on an evaluation project', function () {
    $user = User::factory()->create()->assignRole('collaborateur');
    $project = Project::factory()->evaluation()->create();

    $response = $this->actingAs($user)->post(route('projects.comments.store', $project), [
        'content' => 'Not allowed',
        'stage' => 'evaluation',
    ]);

    $response->assertForbidden();
    expect($project->comments()->count())->toBe(0);
});
