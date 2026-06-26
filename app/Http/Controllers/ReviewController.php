<?php

namespace App\Http\Controllers;

use App\Filters\ProjectFilter;
use App\Http\Requests\FilterProjectsRequest;
use App\Models\Project;
use App\Models\States\ModificationState;
use App\Models\States\SubmittedState;
use App\Models\User;

class ReviewController extends Controller
{
    public function __construct(
        private readonly ProjectFilter $filter
    ) {}

    public function index(FilterProjectsRequest $request)
    {
        $projects = $this->filter->apply(
            Project::with(['proposer', 'leader', 'evaluation'])
                ->whereState('status', [SubmittedState::class, ModificationState::class]),
            $request
        )->get();

        $users = User::query()->select('id', 'name')->orderBy('name')->get();

        return view('review', compact('projects', 'users'));
    }
}