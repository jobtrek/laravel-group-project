<?php

use App\Jobs\SendMailProcess;
use App\Jobs\SendStrongerMailProcess;
use App\Models\Project;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('mail:send-reminders', function () {
    $reminderAfterMonths = (int) config('projects.reminder_after_months', 1);

    $projects = Project::with('leader')
        ->needingProgressReminder()
        ->get()
        ->filter(function (Project $project) use ($reminderAfterMonths) {
            $noRecentComment = $project->last_leader_comment_at === null
                || $project->last_leader_comment_at->lt(now()->subMonths($reminderAfterMonths));

            if (! $noRecentComment) {
                return false;
            }

            // Only send the friendly reminder once per silence period: skip
            // projects that were already reminded since the leader's last
            // comment, so this weekly job can't keep refreshing
            // last_reminder_at and starving the escalation email.
            if ($project->last_reminder_at === null) {
                return true;
            }

            return $project->last_leader_comment_at !== null
                && $project->last_leader_comment_at->gt($project->last_reminder_at);
        });

    foreach ($projects as $project) {
        if ($project->leader) {
            SendMailProcess::dispatch($project->leader);
            $project->timestamps = false;
            $project->forceFill([
                'last_reminder_at' => now(),
                'reminder_escalated_at' => null,
            ])->saveQuietly();
        }
    }

    $this->info("Friendly reminders queued for {$projects->count()} project(s).");
})->purpose('Queue friendly progress reminder emails to active project leaders');

Artisan::command('mail:send-warnings', function () {
    $escalationAfterWeeks = (int) config('projects.escalation_after_weeks', 1);

    $overdueProjects = Project::with('members')
        ->needingProgressReminder()
        ->whereNotNull('last_reminder_at')
        ->whereNull('reminder_escalated_at')
        ->where('last_reminder_at', '<', now()->subWeeks($escalationAfterWeeks))
        ->get()
        ->filter(fn (Project $project) => $project->last_leader_comment_at === null
            || $project->last_leader_comment_at->lt($project->last_reminder_at));

    foreach ($overdueProjects as $project) {
        SendStrongerMailProcess::dispatch($project);
        $project->timestamps = false;
        $project->forceFill(['reminder_escalated_at' => now()])->saveQuietly();
    }

    $this->info("Warning emails queued for {$overdueProjects->count()} project(s).");
})->purpose('Queue firm warning emails for projects overdue for a progress update');

// It checks if any of the projects meet the criteria within a weekly basis.
Schedule::command('mail:send-reminders')->weeklyOn(1, '09:00');
Schedule::command('mail:send-warnings')->weeklyOn(3, '09:00');
Schedule::command('projects:auto-archive')->dailyAt('00:00')->withoutOverlapping();
