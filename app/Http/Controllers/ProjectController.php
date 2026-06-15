<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::select('id', 'title', 'description')->with("evaluation")->get();
        $users = User::select('id', 'name')->get();

        return view('dashboard', ['projects' => $projects, 'users' => $users]);
    }
}
