<?php

use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

beforeEach(function () {
    Mail::fake();
    Permission::firstOrCreate(['name' => 'review', 'guard_name' => 'web']);
    Permission::firstOrCreate(['name' => 'manage everything', 'guard_name' => 'web']);
});

it('debugs the gate', function () {
    $direction = User::factory()->create();
    $direction->givePermissionTo('review');

    $project = Project::factory()->evaluation()->create(['proposer_id' => $direction->id]);

    dump([
        'proposer_id' => $project->proposer_id,
        'user_id' => $direction->id,
        'gate_middleware_check' => Gate::check('review'),
        'gate_authorize_with_model' => Gate::allows('review', $project),
        'policy_exists' => get_class(Gate::getPolicyFor(Project::class)),
    ]);

    $this->assertTrue(true);
});
