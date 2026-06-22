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

        return view('allProjects', compact('projects', 'counts'));    }
}
