<?php

use App\Jobs\SendMailProcess;
use App\Jobs\SendStrongerMailProcess;
use App\Models\Project;
use App\Models\States\EncoursState;
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
        ->filter(fn (Project $project) => $project->last_leader_comment_at === null
            || $project->last_leader_comment_at->lt(now()->subMonths($reminderAfterMonths)));

    foreach ($projects as $project) {
        if ($project->leader) {
            SendMailProcess::dispatch($project->leader);
            $project->timestamps = false;
            $project->forceFill(['last_reminder_at' => now()])->saveQuietly();
        }
    }

    $this->info("Friendly reminders queued for {$projects->count()} project(s).");
})->purpose('Queue friendly progress reminder emails to active project leaders');

Artisan::command('mail:send-warnings', function () {
    $overdueProjects = Project::with('members')
        ->whereState('status', EncoursState::class)
        ->whereNotNull('last_reminder_at')
        ->where('last_reminder_at', '<', now()->subWeeks((int) config('projects.escalation_after_weeks', 1)))->whereColumn('updated_at', '<', 'last_reminder_at')
        ->get();

    foreach ($overdueProjects as $project) {
        SendStrongerMailProcess::dispatch($project);
        $project->timestamps = false;
        $project->forceFill(['last_reminder_at' => now()])->saveQuietly();
    }

    $this->info("Warning emails queued for {$overdueProjects->count()} project(s).");
})->purpose('Queue firm warning emails for projects overdue for a progress update');

// It checks if any of the projects meet the criteria within a weekly basis.
Schedule::command('mail:send-reminders')->weeklyOn(1, '09:00');
Schedule::command('mail:send-warnings')->weeklyOn(3, '09:00');
Schedule::command('projects:auto-archive')->dailyAt('00:00');
