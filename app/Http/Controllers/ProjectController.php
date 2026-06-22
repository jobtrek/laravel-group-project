<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\User



class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::select('id', 'title', 'description')->with('evaluation')->get();
        $user = User::select('id', 'name')->get();

        return view('dashboard', compact('projects', 'user'));
    }
}
