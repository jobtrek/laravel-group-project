<?php

use App\Jobs\SendMailProcess;
use App\Jobs\SendStrongerMailProcess;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Bus;

it('sends the friendly reminder once and only escalates after a week without activity', function () {
    Bus::fake();

    $leader = User::factory()->create();

    $project = Project::factory()->encours()->create([
        'leader_id' => $leader->id,
        'updated_at' => now()->subMonths(2),
        'last_reminder_at' => null,
    ]);

    Artisan::call('mail:send-reminders');
    Bus::assertDispatched(SendMailProcess::class);
    Bus::assertNotDispatched(SendStrongerMailProcess::class);

    $project->refresh();
    expect($project->last_reminder_at)->not->toBeNull();
    expect($project->reminder_escalated_at)->toBeNull();

    $firstReminderAt = $project->last_reminder_at;

    // A few days later, still no comment: the weekly reminder job must not
    // reset last_reminder_at, otherwise the escalation clock never ages.
    $this->travel(3)->days();
    Artisan::call('mail:send-reminders');
    Bus::assertDispatchedTimes(SendMailProcess::class, 1);

    $project->refresh();
    expect($project->last_reminder_at->equalTo($firstReminderAt))->toBeTrue();

    // Warnings should not fire before the escalation window has elapsed.
    Artisan::call('mail:send-warnings');
    Bus::assertNotDispatched(SendStrongerMailProcess::class);

    // A full week after the first reminder, with still no comment, the
    // stronger escalation email must fire exactly once.
    $this->travel(5)->days();
    Artisan::call('mail:send-warnings');
    Bus::assertDispatchedTimes(SendStrongerMailProcess::class, 1);

    $project->refresh();
    expect($project->last_reminder_at->equalTo($firstReminderAt))->toBeTrue();
    expect($project->reminder_escalated_at)->not->toBeNull();

    // Running both jobs again afterwards must not refire either email for
    // the same silence period.
    Artisan::call('mail:send-reminders');
    Artisan::call('mail:send-warnings');
    Bus::assertDispatchedTimes(SendMailProcess::class, 1);
    Bus::assertDispatchedTimes(SendStrongerMailProcess::class, 1);
});

it('starts a fresh reminder cycle after the leader comments', function () {
    Bus::fake();

    $leader = User::factory()->create();

    $project = Project::factory()->encours()->create([
        'leader_id' => $leader->id,
        'updated_at' => now()->subMonths(2),
        'last_reminder_at' => now()->subWeeks(2),
        'reminder_escalated_at' => now()->subWeeks(1),
    ]);

    // Leader comments after being escalated: this should reset the cycle.
    $project->comments()->create([
        'content' => 'Working on it',
        'stage' => 'en_cours',
        'user_id' => $leader->id,
    ]);

    Artisan::call('mail:send-reminders');
    Bus::assertNotDispatched(SendMailProcess::class);

    // A month later, still no further comment: a brand-new reminder must
    // be sent, and the escalation flag must be clear again.
    $this->travel(1)->month();
    Artisan::call('mail:send-reminders');
    Bus::assertDispatched(SendMailProcess::class);

    $project->refresh();
    expect($project->reminder_escalated_at)->toBeNull();
});
