<?php

use App\Enums\Role;
use App\Jobs\SendMailProcess;
use App\Jobs\SendStrongerMailProcess;
use App\Mail\StrongerEmailReminder;
use App\Models\Project;
use App\Models\States\EncoursState;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Mail;

/**
 * Posts a comment as the project leader, which resets the inactivity clock.
 */
function leaderComment(Project $project): void
{
    $project->comments()->create([
        'content' => 'Still working on it.',
        'stage' => EncoursState::getMorphClass(),
        'user_id' => $project->leader_id,
    ]);
}

it('reminds once and escalates exactly once a week later', function () {
    Bus::fake();

    $leader = User::factory()->create();

    $project = Project::factory()->encours()->create([
        'leader_id' => $leader->id,
        'updated_at' => now()->subMonths(2),
        'last_reminder_at' => null,
        'escalated_at' => null,
    ]);

    Artisan::call('mail:send-reminders');
    Bus::assertDispatchedTimes(SendMailProcess::class, 1);
    Bus::assertNotDispatched(SendStrongerMailProcess::class);

    $project->refresh();
    $firstReminderAt = $project->last_reminder_at;
    expect($firstReminderAt)->not->toBeNull()
        ->and($project->escalated_at)->toBeNull();

    // Three days on, still silent. The weekly job must not re-stamp
    // last_reminder_at — that is what starved the escalation (issue #306).
    $this->travel(3)->days();
    Artisan::call('mail:send-reminders');
    Bus::assertDispatchedTimes(SendMailProcess::class, 1);
    expect($project->refresh()->last_reminder_at->equalTo($firstReminderAt))->toBeTrue();

    // The one-week escalation window has not elapsed yet.
    Artisan::call('mail:send-warnings');
    Bus::assertNotDispatched(SendStrongerMailProcess::class);

    // Eight days after the reminder, still no comment: the stronger email fires.
    $this->travel(5)->days();
    Artisan::call('mail:send-warnings');
    Bus::assertDispatchedTimes(SendStrongerMailProcess::class, 1);

    $project->refresh();
    expect($project->escalated_at)->not->toBeNull()
        ->and($project->last_reminder_at->equalTo($firstReminderAt))->toBeTrue();

    // A further week of silence must not refire either email for this period.
    $this->travel(7)->days();
    Artisan::call('mail:send-reminders');
    Artisan::call('mail:send-warnings');
    Bus::assertDispatchedTimes(SendMailProcess::class, 1);
    Bus::assertDispatchedTimes(SendStrongerMailProcess::class, 1);
});

it('starts a fresh reminder cycle once the leader comments again', function () {
    Bus::fake();

    $leader = User::factory()->create();

    $project = Project::factory()->encours()->create([
        'leader_id' => $leader->id,
        'updated_at' => now()->subMonths(2),
        'last_reminder_at' => now()->subWeeks(2),
        'escalated_at' => now()->subWeek(),
    ]);

    // The leader finally answers, which resets the inactivity clock.
    leaderComment($project);

    Artisan::call('mail:send-reminders');
    Bus::assertNotDispatched(SendMailProcess::class);

    // A month of renewed silence starts a brand-new cycle, clearing escalated_at.
    $this->travel(2)->months();
    Artisan::call('mail:send-reminders');
    Bus::assertDispatchedTimes(SendMailProcess::class, 1);

    $project->refresh();
    expect($project->escalated_at)->toBeNull()
        ->and($project->last_reminder_at->greaterThan(now()->subMinute()))->toBeTrue();

    // ... and that new cycle escalates again a week later.
    $this->travel(8)->days();
    Artisan::call('mail:send-warnings');
    Bus::assertDispatchedTimes(SendStrongerMailProcess::class, 1);
});

it('does not escalate when the leader comments after the reminder', function () {
    Bus::fake();

    $leader = User::factory()->create();

    $project = Project::factory()->encours()->create([
        'leader_id' => $leader->id,
        'updated_at' => now()->subMonths(2),
        'last_reminder_at' => null,
        'escalated_at' => null,
    ]);

    Artisan::call('mail:send-reminders');
    Bus::assertDispatchedTimes(SendMailProcess::class, 1);

    // The leader replies three days after the reminder.
    $this->travel(3)->days();
    leaderComment($project);

    // Well past the escalation window, but the silence was broken.
    $this->travel(10)->days();
    Artisan::call('mail:send-warnings');
    Bus::assertNotDispatched(SendStrongerMailProcess::class);

    expect($project->refresh()->escalated_at)->toBeNull();
});

it('CCs only users with the project manager role on the stronger reminder', function () {
    Mail::fake();
    $this->seed(RoleAndPermissionSeeder::class);

    $leader = User::factory()->create();
    $member = User::factory()->create();
    $recolteManager = User::factory()->create();
    $projectManager = User::factory()->create();
    $projectManager->assignRole(Role::ProjectManager->value);

    $project = Project::factory()->encours()->create([
        'leader_id' => $leader->id,
        'recolte_manager_id' => $recolteManager->id,
    ]);
    $project->members()->attach($member);

    (new SendStrongerMailProcess($project))->handle();

    Mail::assertSent(StrongerEmailReminder::class, function ($mail) use ($leader, $projectManager, $member, $recolteManager) {
        $cc = collect($mail->cc)->pluck('address');

        return $mail->hasTo($leader->email)
            && $cc->contains($projectManager->email)
            && ! $cc->contains($member->email)
            && ! $cc->contains($recolteManager->email);
    });
});
