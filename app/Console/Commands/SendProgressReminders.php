<?php

namespace App\Console\Commands;

use App\Jobs\SendMailProcess;
use App\Models\Project;
use Illuminate\Console\Command;

class SendProgressReminders extends Command
{
    protected $signature = 'mail:send-reminders';

    protected $description = 'Queue friendly progress reminder emails to active project leaders';

    public function handle(): int
    {
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

        return self::SUCCESS;
    }
}
