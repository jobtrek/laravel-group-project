<?php

namespace App\Http\Controllers;

use App\Actions\SubmitRevisionAction;
use App\Http\Requests\RevisionRequest;
use App\Models\Project;
use App\Models\States\RevisionState;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RevisionController extends Controller
{
    public function showForm(Project $project): View|RedirectResponse
    {
        if ($project->proposer_id !== auth()->id() && ! auth()->user()->can('manage everything')) {
            abort(403);
        }

        if (! $project->status instanceof RevisionState) {
            return redirect()->route('propositions');
        }

        $project->load(['evaluation', 'phases.resources']);

        $comments = $project->comments()
            ->where('stage', 'review')
            ->get()
            ->keyBy('field_key');

        if ($comments->isEmpty()) {
            return redirect()->route('propositions');
        }

        return view('revision-form', compact('project', 'comments'));
    }

    public function submit(
        RevisionRequest $request,
        Project $project,
        SubmitRevisionAction $action,
    ): RedirectResponse {
        if ($project->proposer_id !== auth()->id() && ! auth()->user()->can('manage everything')) {
            abort(403);
        }

        if (! $project->status instanceof RevisionState) {
            return redirect()->route('propositions');
        }

        $action->execute(
            project: $project,
            corrections: $request->validated()['corrections'],
        );

        return redirect()->route('propositions')
            ->with('success', 'Votre proposition a été mise à jour et renvoyée pour évaluation.');
    }
}
