<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Models\Comment;

class CommentController extends Controller
{
    public function store(CommentRequest $request)
    {
        $validatedData = $request->validated();
        // need add checking role of user if he is allowed to comment on the project or not when role is created

        // Create a new comment using the validated data

        $comment = Comment::create([
            'content' => $validatedData['content'],
            'stage' => $validatedData['stage'],
            'user_id' => auth()->id(),
            'project_id' => $validatedData['project_id'],
        ]);

        return redirect()->back()->with('success', 'Comment added successfully.');
    }
}
