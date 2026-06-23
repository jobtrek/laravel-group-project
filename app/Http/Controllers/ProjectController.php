<?php

namespace App\Http\Controllers;

use App\Mail\ApprovedEmail;
use App\Mail\DeniedEmail;
use App\Models\Project;
use App\Service\ProjectService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;

class ProjectController extends Controller
{
    public function index()
    {
        return view('allProjects', ProjectService::getAllProjectsWithUsers());
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
