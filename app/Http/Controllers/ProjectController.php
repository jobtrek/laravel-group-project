<?php

namespace App\Http\Controllers;

use App\Enums\Stage;
use App\Filters\ProjectFilter;
use App\Http\Requests\FilterProjectsRequest;
use App\Mail\ApprovedEmail;
use App\Mail\DeniedEmail;
use App\Models\Project;
use App\Models\User;
use App\Service\ProjectService;
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
            // No whereState() here — allProjects shows every state
            Project::with(['proposer', 'leader', 'evaluation']),
            $request
        )->get();

        $projects = Project::with(['proposer', 'leader', 'evaluation'])->paginate(10);
        $counts = Project::statusCounts();
        $users = User::query()->select('id', 'name')->orderBy('name')->get();

        return view('allProjects', compact('projects', 'counts', 'users'));
    }

    public function stage(Stage $stage)
    {
        $projects = Project::whereState('status', $stage->statuses())
            ->with(['proposer', 'leader', 'evaluation'])
            ->paginate(10);

        return view('stage', compact('projects', 'stage'));
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

    public function requestMoreInfo(Project $project)
    {
        ProjectService::requestMoreInfo($project);

        return Redirect::back()->with('status', 'more-info-requested');
    }

    public function reSubmit(Project $project)
    {
        ProjectService::reSubmit($project);

        return Redirect::back()->with('status', 'project-resubmitted');
    }
}
