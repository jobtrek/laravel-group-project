<?php

namespace App\Http\Controllers;

use App\Actions\RequestMoreInfoAction;
use App\Actions\UpdateProjectAction;
use App\Filters\ProjectFilter;
use App\Http\Requests\FilterProjectsRequest;
use App\Http\Requests\RequestMoreInfoRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use App\Models\ProjectPhase;
use App\Models\States\ArchiveState;
use App\Models\States\CompleteState;
use App\Models\States\EvaluationState;
use App\Models\User;
use App\Service\ProjectService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;

class ProjectController extends Controller
{
    public function __construct(
        private readonly ProjectFilter $filter
    ) {}

    public function index(FilterProjectsRequest $request)
    {
        $projects = $this->filter->apply(
            Project::with(['proposer', 'leader', 'evaluation', 'phases.resources', 'phases.contributions'])
                ->whereNotState('status', [ArchiveState::class, CompleteState::class]),
            $request
        )->paginate((int) config('projects.per_page', 10))->withQueryString();

        $counts = Project::statusCounts();

        $users = User::query()->select('id', 'name')->orderBy('name')->get();

        return view('allProjects', compact('projects', 'counts', 'users'));
    }

    public function approve(Project $project)
    {
        Gate::authorize('review', $project);

        ProjectService::approve($project);

        return Redirect::back()->with('status', 'Le projet a été validé');
    }

    public function deny(Project $project)
    {
        Gate::authorize('review', $project);
        abort_if(! $project->status instanceof EvaluationState, 403);
        ProjectService::deny($project);

        return Redirect::back()->with('status', 'Projet refusé et archivé avec succès');
    }

    public function sendToDirection(Project $project)
    {
        ProjectService::review($project);

        return Redirect::back()->with('status', 'Projet soumis pour évaluation');
    }

    public function requestMoreInfo(
        RequestMoreInfoRequest $request,
        Project $project,
        RequestMoreInfoAction $action,
    ): RedirectResponse {
        Gate::authorize('review', $project);

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
        Gate::authorize('resubmit', $project);

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
        abort_if($phase->project_id !== $project->id, 404);

        $phase->load(['resources', 'contributions', 'itemCompletions']);

        $phaseNumber = $project->phases->pluck('id')->search($phase->id) + 1;

        return view('phase_details', compact('phase', 'project', 'phaseNumber'));
    }

    public function edit(Project $project)
    {
        Gate::authorize('update', $project);

        $project->load(['phases.resources', 'evaluation', 'members']);
        $users = User::query()->select('id', 'name')->orderBy('name')->get();

        return view('edit', compact('project', 'users'));
    }

    public function update(UpdateProjectRequest $request, Project $project, UpdateProjectAction $action)
    {
        $action->execute($project, $request->validated());

        return redirect()->route('projects-details', $project)
            ->with('status', 'project-updated');
    }

    public function complete(Project $project)
    {
        Gate::authorize('complete', $project);

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
