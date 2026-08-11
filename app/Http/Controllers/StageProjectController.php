<?php

namespace App\Http\Controllers;

use App\Enums\Stage;
use App\Filters\ProjectFilter;
use App\Http\Requests\FilterProjectsRequest;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
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

    protected function baseQuery(): Builder
    {
        return Project::with([
            'proposer', 'leader', 'evaluation', 'phases.resources', 'phases.contributions',
        ])->whereState('status', $this->states());
    }

    protected function myProposals(): bool
    {
        return false;
    }

    public function index(FilterProjectsRequest $request): View
    {
        $projects = $this->filter->apply($this->baseQuery(), $request)->paginate((int) config('projects.per_page', 10)) ->withQueryString();
        $users = User::query()->select('id', 'name')->orderBy('name')->get();

        return view('stage', [
            'stage' => $this->stage(),
            'projects' => $projects,
            'users' => $users,
            'myProposals' => $this->myProposals(),
        ]);
    }
}
