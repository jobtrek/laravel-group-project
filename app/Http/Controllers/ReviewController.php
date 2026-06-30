<?php

namespace App\Http\Controllers;

use App\Enums\Stage;
use App\Filters\ProjectFilter;
use App\Http\Requests\FilterProjectsRequest;
use App\Models\Project;
use App\Models\States\EvaluationState;
use App\Models\User;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function __construct(
        private readonly ProjectFilter $filter
    ) {}

    public function index(FilterProjectsRequest $request)
    {
        $projects = $this->filter->apply(
            Project::with(['proposer', 'leader', 'evaluation'])
                ->whereState('status', [EvaluationState::class]),
            $request
        )->paginate(10);

        $users = User::query()->select('id', 'name')->orderBy('name')->get();

        return view('stage', [
            'stage' => Stage::Evaluation,
            'projects' => $projects,
            'users' => $users,
        ]);
    }
    public function showForm(Project $project): View
    {
        $project->load(['proposer', 'evaluation', 'phases.resources', 'members']);

        return view('review-form', compact('project'));
    }
}
