<?php

namespace App\Http\Controllers;

use App\Actions\CreateProjectProposal;
use App\Enums\Stage;
use App\Filters\ProjectFilter;
use App\Http\Requests\FilterProjectsRequest;
use App\Http\Requests\PropositionRequest;
use App\Models\Project;
use App\Models\States\PropositionState;
use App\Models\States\RevisionState;
use App\Models\User;

class PropositionController extends Controller
{
    public function __construct(
        private readonly ProjectFilter $filter
    ) {}

    public function index(FilterProjectsRequest $request)
    {
        $projects = $this->filter->apply(
            Project::with(['proposer', 'leader', 'evaluation'])
                ->whereState('status', [PropositionState::class, RevisionState::class]),
            $request
        )->paginate(10);

        $users = User::query()->select('id', 'name')->orderBy('name')->get();

        return view('stage', [
            'stage' => Stage::Propositions,
            'projects' => $projects,
            'users' => $users,
        ]);
    }

    public function store(PropositionRequest $request, CreateProjectProposal $action)
    {
        $action->execute($request->validated(), auth()->id());

        return redirect()->route('dashboard');
    }
}