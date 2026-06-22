<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with(['proposer', 'leader'])->get();
        $counts = Project::statusCounts();

        return view('allProjects', compact('projects', 'counts'));
    }

    public function approve(Project $project)
    {
        $project->status->transitionTo(ApprovedState::class);
        $project->save();

        return Redirect::back()->with('status', 'project-approved');
    }

    public function deny(Project $project)
    {
        $project->status->transitionTo(RefusedState::class);
        $project->save();

        return Redirect::back()->with('status', 'project-denied');
    }

    public function requestMoreInfo(Project $project)
    {
        $project->status->transitionTo(ModificationState::class);
        $project->save();

        return Redirect::back()->with('status', 'more-info-requested');
    }

    public function reSubmit(Project $project)
    {
        $project->status->transitionTo(SubmittedState::class);
        $project->save();

        return Redirect::back()->with('status', 'project-resubmitted');
    }
}
