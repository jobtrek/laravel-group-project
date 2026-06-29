<?php

namespace App\Http\Controllers;

use App\Enums\Stage;
use App\Filters\ProjectFilter;
use App\Http\Requests\FilterProjectsRequest;
use App\Models\Project;
use App\Models\User;
use App\Models\States\RecolteState;
use App\Models\States\EncoursState;
use Illuminate\Http\Request;

class RecolteController extends Controller
{
    public function __construct(
        private readonly ProjectFilter $filter
    ) {}

    public function index(FilterProjectsRequest $request)
    {
        $projects = $this->filter->apply(
            Project::with(['proposer', 'leader', 'evaluation'])
                ->whereState('status', [RecolteState::class]),
            $request
        )->paginate(10);

        $users = User::query()->select('id', 'name')->orderBy('name')->get();

        return view('stage', [
            'stage' => Stage::Recolte,
            'projects' => $projects,
            'users' => $users,
        ]);
    }

    public function moveFromRecolteToActive(Request $request)
    {
        $recolteId = $request->input('recolte_id');
        $project = Project::findOrFail($recolteId);

        if ($project->progress >= 80) {
            $project->status->transitionTo(EncoursState::class);
            $project->save();

            return redirect()->back()->with('success', 'Project moved to Active state successfully.');
        } else {
            return redirect()->back()->with('error', 'Project cannot be moved to Active state. Progress must be greater than 80%.');
        }
    }
}
