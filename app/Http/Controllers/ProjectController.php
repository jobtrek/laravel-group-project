<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;

class ProjectController extends Controller
{
    public function listingProjects()
    {
        $projects = Project::select('id', 'title', 'description')->with("evaluations")->get();
    }
}
