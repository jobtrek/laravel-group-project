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

    // A repeat run of the reminder command in the same window must not reset last_reminder_at.
    $firstReminderAt = $project->last_reminder_at;
    Artisan::call('mail:send-reminders');
    $project->refresh();
    expect($project->last_reminder_at->equalTo($firstReminderAt))->toBeTrue();

    // Warnings should not fire before the escalation window has elapsed.
    Artisan::call('mail:send-warnings');
    Bus::assertNotDispatched(SendStrongerMailProcess::class);

    // Simulate a full week passing with no further activity on the project.
    $project->timestamps = false;
    $project->forceFill(['last_reminder_at' => now()->subWeeks(2)])->saveQuietly();

    Artisan::call('mail:send-warnings');
    Bus::assertDispatched(SendStrongerMailProcess::class);
});
