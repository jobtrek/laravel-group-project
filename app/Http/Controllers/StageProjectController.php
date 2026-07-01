<?php

namespace App\Http\Controllers;

use App\Enums\Stage;
use App\Filters\ProjectFilter;
use App\Http\Requests\FilterProjectsRequest;
use App\Models\Project;
use App\Models\User;
use Illuminate\View\View;

abstract class StageProjectController extends Controller
{
    public function __construct(
        protected readonly ProjectFilter $filter
    ) {}

    abstract protected function stage(): Stage;

    /**
     * @return class-string|array<class-string>
     */
    abstract protected function states(): string|array;

    public function index(FilterProjectsRequest $request): View
    {
        $projects = $this->filter->apply(
            Project::with(['proposer', 'leader', 'evaluation', 'phases.resources'])
                ->whereState('status', $this->states()),
            $request
        )->paginate(10);

        $users = User::query()->select('id', 'name')->orderBy('name')->get();

        return view('stage', [
            'stage' => $this->stage(),
            'projects' => $projects,
            'users' => $users,
        ]);
    }
}
