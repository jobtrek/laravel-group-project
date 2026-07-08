<?php

use App\Http\Middleware\EnsureProjectHasLeader;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;

function makeRequestForProject(?Project $project): Request
{
    $request = Request::create('/projects/1/move-to-en-cours', 'PATCH');
    $route = (new Route('PATCH', '/projects/{project}/move-to-en-cours', []))->bind($request);
    $route->setParameter('project', $project);
    $request->setRouteResolver(fn () => $route);

    return $request;
}

it('blocks the request when the project has no leader', function () {
    User::factory()->create();
    $middleware = new EnsureProjectHasLeader;
    $project = Project::factory()->recolte()->create(['leader_id' => null]);

    $called = false;
    $response = $middleware->handle(makeRequestForProject($project), function () use (&$called) {
        $called = true;
    });

    expect($called)->toBeFalse();
    expect($response->getStatusCode())->toBe(302);
});

it('lets the request through when the project has a leader', function () {
    $middleware = new EnsureProjectHasLeader;
    $leader = User::factory()->create();
    $project = Project::factory()->recolte()->create(['leader_id' => $leader->id]);

    $called = false;
    $middleware->handle(makeRequestForProject($project), function () use (&$called) {
        $called = true;

        return response('ok');
    });

    expect($called)->toBeTrue();
});
