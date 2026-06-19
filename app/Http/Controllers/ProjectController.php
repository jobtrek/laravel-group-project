<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    private function filterProjects(Request $request)
    {
        $validated = $request->validate([
            'status' => 'string',
            'score' => 'integer',
            'date' => 'date',
            'proposer' => 'exists:users,id',
        ]);

        return Project::query()
            ->select('id', 'title', 'description', 'proposer_id')
            ->with('evaluation', 'proposer:id,name')
            ->when($request->filled('status'), fn($q) => $q->status($validated['status']))
            ->when($request->filled('score'), fn($q) => $q->score($validated['score']))
            ->when($request->filled('date'), fn($q) => $q->date($validated['date']))
            ->when($request->filled('proposer'), fn($q) => $q->proposer($validated['proposer']))
            ->get();
    }

    public function index(Request $request)
    {
        $projects = $this->filterProjects($request);
        $users = User::select('id', 'name')->get();

        return view('dashboard', compact('projects', 'users'));
    }
}
