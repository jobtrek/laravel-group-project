<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use app\Models\User;

class ProjectController extends Controller
{
    public function listingProjects()
    {
        $projects = Project::select('id', 'title', 'description')->with("evaluation")->get();
        $users = User::query()->select('id', 'name')->get();

        return view('dashboard', ['projects' => $projects, 'users' => $users]);
    }
}
