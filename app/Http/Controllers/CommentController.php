<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Models\Comment;
use App\Models\Project;

class CommentController extends Controller
{
    public function store(CommentRequest $request, Project $project)
    {
        $validatedData = $request->validated();

        Comment::create([
            'content' => $validatedData['content'],
            'stage' => $validatedData['stage'],
            'user_id' => auth()->id(),
            'project_id' => $project->id,
        ]);

        return redirect()->back()->with('success', 'Comment added successfully.');
    }
}
