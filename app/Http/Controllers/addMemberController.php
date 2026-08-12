<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;

class addMemberController extends Controller
{
        public function render(Project $project)
    {
        $users = User::all();
        $members = $project->members()->get()->push($project->leader);
        return view('addMembersToProject', compact('project', 'users', 'members'
        ));
    }
}
