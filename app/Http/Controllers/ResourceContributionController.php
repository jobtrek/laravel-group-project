<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreResourceContributionRequest;
use App\Models\Project;
use App\Models\ProjectPhase;
use App\Models\ResourceContribution;
use App\Models\States\EncoursState;
use App\Models\States\RecolteState;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

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
        $phaseIds = $project->phases->pluck('id');

        // Lock all contribution rows for every phase of this project —
        // concurrent requests block here until this transaction commits.
        $totalFound = ResourceContribution::whereIn('phase_id', $phaseIds)
            ->lockForUpdate()
            ->sum('amount');

        $totalNeeded = $project->phases
            ->load('resources')
            ->sum(fn (ProjectPhase $phase): float => $phase->amount_needed);

        if ($totalNeeded > 0) {
            $newProgress = (($totalFound + (float) $request->validated('amount')) / $totalNeeded) * 100;

            if ($newProgress > 200) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'amount' => sprintf(
                        'This contribution would exceed the 200%% project cap (%.2f remaining).',
                        ($totalNeeded * 2) - $totalFound
                    ),
                ]);
            }
        }

        ResourceContribution::create([
            'phase_id'      => $request->validated('phase_id'),
            'user_id'       => auth()->id(),
            'resource_type' => $request->validated('resource_type'),
            'description'   => $request->validated('description'),
            'amount'        => $request->validated('amount'),
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
