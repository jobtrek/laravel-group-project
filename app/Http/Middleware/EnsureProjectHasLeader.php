<?php

namespace App\Http\Middleware;

use App\Models\Project;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProjectHasLeader
{
    public function handle(Request $request, Closure $next): Response
    {
        $project = $request->route('project');

        $project = $request->route('project');

        if (! $project instanceof Project || $project->leader_id === null) {
            return back()->with('error', 'Un chef de projet doit être assigné avant de démarrer le projet.');
        }

        return $next($request);
    }
}
