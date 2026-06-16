<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\User;

class ProjectController extends Controller
{
    private function filterProjects(Request $request)
    {
        $request->validate([
            'status' => 'nullable|string',
            'score' => 'nullable|integer',
            'date' => 'nullable|date',
            'proposer' => 'nullable|exists:users,id',
        ]);



        $query = Project::query()->select('id', 'title', 'description', 'proposer_id')->with('evaluation', 'proposer:id,name');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('score')) {
            $query->whereHas('evaluation', function ($q) use ($request) {
                $q->where('importance', '>=', $request->score);
            });
        }
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        if ($request->filled('proposer')) {
            $query->where('proposer_id', $request->proposer);
        }

        return $query->get();
    }

    public function index(Request $request)
    {
        $projects = $this->filterProjects($request);
        $users = User::select('id', 'name')->get();

        return view('dashboard', [
            'projects' => $projects,
            'users' => $users,
        ]);
    }
}
