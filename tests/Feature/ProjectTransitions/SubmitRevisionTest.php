<?php

use App\Actions\SubmitRevisionAction;
use App\Models\Comment;
use App\Models\Project;
use App\Models\States\PropositionState;
use App\Models\User;

it('applies only whitelisted corrections, clears review comments, and transitions back to Proposition', function () {
    $project = Project::factory()->revision()->create(['title' => 'Old title']);
    $reviewer = User::factory()->create();

    Comment::create([
        'content' => 'Fix the title',
        'field_key' => 'title',
        'stage' => 'review',
        'user_id' => $reviewer->id,
        'project_id' => $project->id,
    ]);

    (new SubmitRevisionAction)->execute($project, [
        'title' => 'New title',
        'description' => 'Not whitelisted, should be ignored',
    ]);

    $project->refresh();

    expect($project->title)->toBe('New title')
        ->and($project->description)->not->toBe('Not whitelisted, should be ignored')
        ->and($project->comments()->where('stage', 'review')->count())->toBe(0)
        ->and($project->status)->toBeInstanceOf(PropositionState::class);
});
