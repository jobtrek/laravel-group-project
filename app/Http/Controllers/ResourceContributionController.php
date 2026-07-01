<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreResourceContributionRequest;
use App\Models\Project;
use App\Models\ProjectPhase;
use App\Models\ResourceContribution;
use App\Models\States\EncoursState;
use App\Models\States\RecolteState;
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

        $project->load(['phases.resources', 'phases.contributions']);

        $phasesData = $project->phases->map(fn (ProjectPhase $phase) => [
            'id' => $phase->id,
            'name' => $phase->name,
            'needed' => $phase->amount_needed,
            'found' => $phase->amount_found,
        ])->values();

        return view('resource-contribution-form', compact('project', 'phasesData'));
    }

    public function store(StoreResourceContributionRequest $request, Project $project): RedirectResponse
    {
        DB::transaction(function () use ($request, $project): void {
            ResourceContribution::create([
                'phase_id' => $request->validated('phase_id'),
                'user_id' => auth()->id(),
                'resource_type' => $request->validated('resource_type'),
                'description' => $request->validated('description'),
                'amount' => $request->validated('amount'),
            ]);

            $project->load(['phases.resources', 'phases.contributions']);

            if ($project->progress >= 80 && $project->status instanceof RecolteState) {
                $project->status->transitionTo(EncoursState::class);
                $project->save();
            }
        });

        $redirectRoute = $project->status instanceof EncoursState
        ? 'en-cours'
        : 'recolte';

        return redirect()
        ->route($redirectRoute)
        ->with('success', 'Resource contribution added successfully.');
    }
}
