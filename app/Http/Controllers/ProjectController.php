<?php

namespace App\Http\Controllers;

use App\Actions\RequestMoreInfoAction;
use App\Actions\UpdateProjectAction;
use App\Filters\ProjectFilter;
use App\Http\Requests\FilterProjectsRequest;
use App\Http\Requests\RequestMoreInfoRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use App\Models\User;
use App\Models\ProjectPhase;
use App\Service\ProjectService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;

class ProjectController extends Controller
{
    public function __construct(
        private readonly ProjectFilter $filter
    )
    {
    }

    public function index(FilterProjectsRequest $request)
    {
        $projects = $this->filter->apply(
            Project::with(['proposer', 'leader', 'evaluation', 'phases.resources', 'phases.contributions']), $request
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
        abort_if($project->proposer_id === auth()->id(), 403);

        ProjectService::approve($project);

        return Redirect::back()->with('status', 'project-approved');
    }

    public function deny(Project $project)
    {
        abort_if($project->proposer_id === auth()->id() || !$project->status instanceof \App\Models\States\EvaluationState, 403);
        ProjectService::deny($project);

        return Redirect::back()->with('status', 'project-denied');
    }

    public function sendToDirection(Project $project)
    {
        ProjectService::review($project);

        return Redirect::back()->with('status', 'project-sent-to-direction');
    }

    public function requestMoreInfo(
        RequestMoreInfoRequest $request,
        Project                $project,
        RequestMoreInfoAction  $action,
    ): RedirectResponse
    {
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
        abort_if($project->proposer_id !== auth()->id() || !$project->status instanceof \App\Models\States\RevisionState, 403);
        ProjectService::reSubmit($project);

        return Redirect::back()->with('status', 'project-resubmitted');
    }

    public function detailPage(Project $project)
    {
        $project->load(['proposer', 'leader', 'evaluation', 'phases', 'phases.resources', 'members', 'comments', 'comments.user']);

        return view('projectsDetails', compact('project'));
    }

    public function phaseDetail(Project $project, ProjectPhase $phase)
    {
        $project->load(['leader', 'phases',]);
        $phase->load(['resources', 'contributions']);

        return view('phase_details', compact('phase', 'project'));
    }

    public function edit(Project $project)
    {
        abort_if(!$project->status->isEditable() || auth()->id() !== $project->proposer_id, 403);

        $project->load(['phases.resources', 'evaluation', 'members']);
        $users = User::query()->select('id', 'name')->orderBy('name')->get();

        return view('edit', compact('project', 'users'));
    }

    public function update(UpdateProjectRequest $request, Project $project, UpdateProjectAction $action)
    {
        abort_if(!$project->status->isEditable() || auth()->id() !== $project->proposer_id, 403);

        $action->execute($project, $request->validated());

        return redirect()->route('projects-details', $project)
            ->with('status', 'project-updated');
    }

    public function complete(Project $project)
    {
        abort_if($project->proposer_id === auth()->id() || !$project->status instanceof \App\Models\States\EvaluationState, 403);
        try {
            ProjectService::complete($project);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('status', 'project-completed');
    }

    public function archive(Project $project)
    {
        ProjectService::archive($project);

        return back()->with('status', 'project-archived');
    }
}
