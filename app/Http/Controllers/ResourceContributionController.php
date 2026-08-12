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
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
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
            // Lock the parent project record to serialize contributions and prevent race conditions
            $lockedProject = Project::lockForUpdate()->findOrFail($project->id);
            $lockedProject->load('phases.resources');

            $phaseId = (int) $request->validated('phase_id');
            $resourceType = $request->validated('resource_type');
            $amount = round((float) $request->validated('amount'), 2);

            $phase = $lockedProject->phases->firstWhere('id', $phaseId);
            $resource = $phase?->resources->firstWhere('resource_type', $resourceType);

            if ($resource) {
                $foundForResource = (float) ResourceContribution::where('phase_id', $phaseId)
                    ->where('resource_type', $resourceType)
                    ->sum('amount');
                $remainingForResource = round(((float) $resource->amount_needed * 2) - $foundForResource, 2);

                if ($amount > $remainingForResource) {
                    throw ValidationException::withMessages([
                        'amount' => sprintf(
                            'This contribution exceeds what is still needed for this resource type (%.2f remaining).',
                            $remainingForResource
                        ),
                    ]);
                }
            }

            $phaseIds = $lockedProject->phases->pluck('id');

            $totalFound = ResourceContribution::whereIn('phase_id', $phaseIds)
                ->sum('amount');

            $totalNeeded = $lockedProject->phases
                ->sum(fn (ProjectPhase $phase): float => $phase->amount_needed);

            if ($totalNeeded > 0) {
                $newProgress = (($totalFound + $amount) / $totalNeeded) * 100;

                if ($newProgress > 200) {
                    throw ValidationException::withMessages([
                        'amount' => sprintf(
                            'This contribution would exceed the 200%% project cap (%.2f remaining).',
                            ($totalNeeded * 2) - $totalFound
                        ),
                    ]);
                }
            }

            ResourceContribution::create([
                'phase_id' => $phaseId,
                'user_id' => auth()->id(),
                'resource_type' => $resourceType,
                'description' => $request->validated('description'),
                'amount' => $amount,
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
