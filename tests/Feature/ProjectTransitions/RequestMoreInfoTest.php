<?php

use App\Actions\RequestMoreInfoAction;
use App\Mail\RequestMoreInfoMail;
use App\Models\Project;
use App\Models\States\RevisionState;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

it('creates review comments, transitions to Revision, and queues a mail to the proposer', function () {
    Mail::fake();

    $project = Project::factory()->evaluation()->create();
    $direction = User::factory()->create();

    (new RequestMoreInfoAction)->execute(
        project: $project,
        fieldComments: ['title' => 'Please clarify the title'],
        directionUserId: $direction->id,
    );

    expect($project->status)->toBeInstanceOf(RevisionState::class)
        ->and($project->comments()->where('stage', 'review')->count())->toBe(1)
        ->and($project->comments()->first()->field_key)->toBe('title');

    Mail::assertQueued(RequestMoreInfoMail::class, fn ($mail) => $mail->hasTo($project->proposer->email));
});
