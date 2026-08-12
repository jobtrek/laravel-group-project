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
            ->filter(fn (Project $project) => $this->isDueForReminder($project, $reminderAfterMonths));

        foreach ($projects as $project) {
            if ($project->leader) {
                SendMailProcess::dispatch($project->leader);
                $project->timestamps = false;
                $project->forceFill([
                    'last_reminder_at' => now(),
                    'escalated_at' => null,
                ])->saveQuietly();
            }
        }

        $this->info("Friendly reminders queued for {$projects->count()} project(s).");

        return self::SUCCESS;
    }

    /**
     * One friendly reminder per silence period. Re-stamping last_reminder_at on
     * every weekly run kept the escalation clock a few days old forever, so the
     * stronger reminder never fired.
     */
    private function isDueForReminder(Project $project, int $reminderAfterMonths): bool
    {
        $lastComment = $project->last_leader_comment_at;

        if ($lastComment !== null && $lastComment->gte(now()->subMonths($reminderAfterMonths))) {
            return false;
        }

        return $project->last_reminder_at === null
            || ($lastComment !== null && $lastComment->gt($project->last_reminder_at));
    }
}
