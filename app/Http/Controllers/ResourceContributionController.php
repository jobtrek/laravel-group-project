<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreResourceContributionRequest;
use App\Models\Project;
use App\Models\ProjectPhase;
use App\Models\States\EncoursState;
use App\Models\States\RecolteState;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ResourceContributionController extends Controller
{
    public function create(Project $project): View
    {
        abort_if(
            ! $project->status instanceof RecolteState &&
            ! $project->status instanceof EncoursState,
            404
        );

        $project->load(['phases.resources', 'phases.contributions', 'leader', 'members']);

        $phasesData = $project->phases->map(fn (ProjectPhase $phase) => [
            'id' => $phase->id,
            'name' => $phase->name,
            'needed' => $phase->amount_needed,
            'found' => $phase->amount_found,
            'resources' => $phase->resources->map(fn ($resource) => [
                'resource_type' => $resource->resource_type,
                'needed' => (float) $resource->amount_needed,
                'found' => (float) $phase->contributions
                    ->where('resource_type', $resource->resource_type)
                    ->sum('amount'),
            ])->values()->all(),
        ])->values();

        $users = User::query()->select('id', 'name')->orderBy('name')->get();

        return view('resource-contribution-form', compact('project', 'phasesData', 'users'));
    }

    public function store(StoreResourceContributionRequest $request, Project $project): RedirectResponse
    {
        DB::transaction(function () use ($request, $project): void {
            // The 200% caps are enforced in StoreResourceContributionRequest; this lock only
            // serializes concurrent inserts so contributions are written in a consistent order.
            Project::lockForUpdate()->findOrFail($project->id);

            ResourceContribution::create([
                'phase_id' => (int) $request->validated('phase_id'),
                'user_id' => auth()->id(),
                'resource_type' => $request->validated('resource_type'),
                'description' => $request->validated('description'),
                'amount' => (float) $request->validated('amount'),
            ]);
        });

        $redirectRoute = $project->status instanceof EncoursState
            ? 'en-cours'
            : 'recolte';

        return redirect()
            ->route($redirectRoute)
            ->with('success', 'Resource contribution added successfully.');
    }
}
