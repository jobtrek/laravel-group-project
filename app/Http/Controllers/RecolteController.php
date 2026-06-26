<?php

namespace App\Http\Controllers;

use App\Filters\ProjectFilter;
use App\Http\Requests\FilterProjectsRequest;
use App\Models\Project;
use App\Models\States\CollectingState;
use App\Models\States\ReadyState;
use App\Models\User;

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
}