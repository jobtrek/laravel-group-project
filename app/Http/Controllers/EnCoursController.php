<?php

namespace App\Http\Controllers;

use App\Filters\ProjectFilter;
use App\Http\Requests\FilterProjectsRequest;
use App\Models\Project;
use App\Models\States\ActiveState;
use App\Models\User;

class EnCoursController extends Controller
{
    public function __construct(
        private readonly ProjectFilter $filter
    ) {}

    public function index(FilterProjectsRequest $request)
    {
        $projects = $this->filter->apply(
            Project::with(['proposer', 'leader', 'evaluation'])
                ->whereState('status', ActiveState::class),
            $request
        )->get();

        $users = User::query()->select('id', 'name')->orderBy('name')->get();

        return view('enCours', compact('projects', 'users'));
    }
}