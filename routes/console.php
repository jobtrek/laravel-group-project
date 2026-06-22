<?php

use App\Jobs\SendMailProcess;
use App\Jobs\SendStrongerMailProcess;
use App\Models\Project;
use App\Models\States\ActiveState;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('mail:send-reminders', function () {
    $projects = Project::with('leader')
        ->whereState('status', ActiveState::class)
        ->get();

    foreach ($projects as $project) {
        SendMailProcess::dispatch($project->leader);
    }

    $this->info("Friendly reminders queued for {$projects->count()} project(s).");
})->purpose('Queue friendly progress reminder emails to active project leaders');

Artisan::command('mail:send-warnings', function () {
    $overdueProjects = Project::with('members')
        ->whereState('status', [ActiveState::class])
        ->where(function ($query) {
            $query->whereNull('last_reminder_at')
                ->orWhere('last_reminder_at', '<', now()->subDays(3));
        })
        ->get();

    foreach ($overdueProjects as $project) {
        SendStrongerMailProcess::dispatch($project);
        $project->update(['last_reminder_at' => now()]);
    }

    $this->info("Warning emails queued for {$overdueProjects->count()} project(s).");
})->purpose('Queue firm warning emails for projects overdue for a progress update');

// it checks if any of the projects meets the criteria within a weekly basic. 
Schedule::command('mail:send-reminders')->weeklyOn(1, '09:00'); 
Schedule::command('mail:send-warnings')->weeklyOn(3, '09:00');  
