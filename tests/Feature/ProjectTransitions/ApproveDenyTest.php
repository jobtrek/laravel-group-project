<?php

use App\Mail\ApprovedEmail;
use App\Mail\DeniedEmail;
use App\Models\Project;
use App\Models\States\ArchiveState;
use App\Models\States\RecolteState;
use App\Service\ProjectService;
use Illuminate\Support\Facades\Mail;

it('approves a project, transitions it to Recolte, and queues an email to the proposer', function () {
    Mail::fake();

    $project = Project::factory()->evaluation()->create();

    ProjectService::approve($project);

    expect($project->status)->toBeInstanceOf(RecolteState::class);
    Mail::assertQueued(ApprovedEmail::class, fn ($mail) => $mail->hasTo($project->proposer->email));
});

it('denies a project, transitions it to Archive, and queues an email to the proposer', function () {
    Mail::fake();

    $project = Project::factory()->proposition()->create();

    ProjectService::deny($project);

    expect($project->status)->toBeInstanceOf(ArchiveState::class);
    Mail::assertQueued(DeniedEmail::class, fn ($mail) => $mail->hasTo($project->proposer->email));
});
