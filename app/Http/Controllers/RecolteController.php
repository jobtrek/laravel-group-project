<?php

namespace App\Http\Controllers;

use App\Filters\ProjectFilter;
use App\Http\Requests\FilterProjectsRequest;
use App\Models\Project;
use App\Models\States\CollectingState;
use App\Models\States\ReadyState;
use App\Models\User;
use App\Models\States\ActiveState;


class RecolteController extends Controller
{
    public function __construct(
        private readonly ProjectFilter $filter
    ) {}

    public function index(FilterProjectsRequest $request)
    {
        $projects = $this->filter->apply(
            Project::with(['proposer', 'leader', 'evaluation'])
                ->whereState('status', [CollectingState::class, ReadyState::class]),
            $request
        )->get();

        $users = User::query()->select('id', 'name')->orderBy('name')->get();

        return view('recolte', compact('projects', 'users'));
    }
    public function moveFromRecolteToActive(Request $request)
    {
        $recolteId = $request->input('recolte_id');
        $project = Project::findOrFail($recolteId);

        if ($project->progress >= 80) {
            $project->status->transitionTo(ActiveState::class);
            $project->save();

            return redirect()->back()->with('success', 'Project moved to Active state successfully.');
        } else {
            return redirect()->back()->with('error', 'Project cannot be moved to Active state. Progress must be greater than 80%.');
        }
    }
}
