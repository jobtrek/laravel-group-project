<?php

namespace App\Http\Controllers;

use App\Models\States\ApprovedState;
use App\Models\States\RefusedState;
use App\Models\States\SubmittedState;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Redirect;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::select('id', 'title', 'description')->with('evaluation')->get();
        $users = User::select('id', 'name')->get();

        return view('dashboard', ['projects' => $projects, 'users' => $users]);
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
}
