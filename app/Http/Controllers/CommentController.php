<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Models\Project;

class CommentController extends Controller
{
    public function store(CommentRequest $request, Project $project)
    {
        $validatedData = $request->validated();

        $project->comments()->create([
            'content' => $validatedData['content'],
            'stage' => $validatedData['stage'],
            'user_id' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Comment added successfully.');
    }
}
