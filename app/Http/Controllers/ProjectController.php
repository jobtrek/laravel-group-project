<?php

namespace App\Http\Controllers;

use App\Actions\RequestMoreInfoAction;
use App\Filters\ProjectFilter;
use App\Http\Requests\FilterProjectsRequest;
use App\Http\Requests\RequestMoreInfoRequest;
use App\Mail\ApprovedEmail;
use App\Mail\DeniedEmail;
use App\Models\Project;
use App\Models\User;
use App\Service\ProjectService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;

class ProjectController extends Controller
{
    public function __construct(
        private readonly ProjectFilter $filter
    ) {}

    public function index(FilterProjectsRequest $request)
    {
        $projects = $this->filter->apply(
            Project::with(['proposer', 'leader', 'evaluation']),
            $request
        )->paginate(10)->withQueryString();

        $counts = Project::statusCounts();
        $users = User::query()->select('id', 'name')->orderBy('name')->get();

        return view('allProjects', compact('projects', 'counts', 'users'));
    }

    public function review(Project $project)
    {
        ProjectService::review($project);

        return Redirect::back()->with('status', 'project-in-review');
    }

    public function approve(Project $project)
    {
        ProjectService::approve($project);
        if ($proposer = $project->proposer) {
            Mail::to($proposer->email)->send(new ApprovedEmail($proposer->name));
        }

        return Redirect::back()->with('status', 'project-approved');
    }

    public function deny(Project $project)
    {
        ProjectService::deny($project);
        if ($proposer = $project->proposer) {
            Mail::to($proposer->email)->send(new DeniedEmail($proposer->name));
        }

        return Redirect::back()->with('status', 'project-denied');
    }

    public function requestMoreInfo(
        RequestMoreInfoRequest $request,
        Project $project,
        RequestMoreInfoAction $action,
    ): RedirectResponse {
        $action->execute(
            project: $project,
            fieldComments: $request->validated()['field_comments'],
            directionUserId: auth()->id(),
        );

        return redirect()->route('evaluation')
            ->with('success', 'Demande d\'informations envoyée au proposeur.');
    }

    public function reSubmit(Project $project)
    {
        ProjectService::reSubmit($project);

        return Redirect::back()->with('status', 'project-resubmitted');
    }
}
