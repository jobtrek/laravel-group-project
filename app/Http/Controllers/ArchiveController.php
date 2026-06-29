<?php

namespace App\Http\Controllers;

use App\Enums\Stage;
use App\Filters\ProjectFilter;
use App\Http\Requests\FilterProjectsRequest;
use App\Models\Project;
use App\Models\States\ArchivedState;
use App\Models\States\CompletedState;
use App\Models\States\RefusedState;
use App\Models\User;

class ArchiveController extends Controller
{
    public function __construct(
        private readonly ProjectFilter $filter
    ) {}

    public function index(FilterProjectsRequest $request)
    {
        $projects = $this->filter->apply(
            Project::with(['proposer', 'leader', 'evaluation'])
                ->whereState('status', [ArchivedState::class, CompletedState::class, RefusedState::class]),
            $request
        )->paginate(10);

        $users = User::query()->select('id', 'name')->orderBy('name')->get();

        return view('stage', [
            'stage' => Stage::Archive,
            'projects' => $projects,
            'users' => $users,
        ]);
    }
}
