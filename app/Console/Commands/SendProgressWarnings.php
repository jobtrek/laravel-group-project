<?php

namespace App\Console\Commands;

use App\Jobs\SendStrongerMailProcess;
use App\Models\Project;
use Illuminate\Console\Command;

class SendProgressWarnings extends Command
{
    protected $signature = 'mail:send-warnings';

    protected $description = 'Queue firm warning emails for projects overdue for a progress update';

    public function handle(): int
    {
        $escalationAfterWeeks = (int) config('projects.escalation_after_weeks', 1);

        $overdueProjects = Project::with('members')
            ->needingProgressReminder()
            ->whereNotNull('last_reminder_at')
            ->whereNull('escalated_at')
            ->where('last_reminder_at', '<', now()->subWeeks($escalationAfterWeeks))
            ->get()
            ->filter(fn (Project $project) => ($project->last_leader_comment_at ?? $project->updated_at)
                ->lt($project->last_reminder_at));

        foreach ($overdueProjects as $project) {
            SendStrongerMailProcess::dispatch($project);
            $project->timestamps = false;
            $project->forceFill(['escalated_at' => now()])->saveQuietly();
        }

        $this->info("Warning emails queued for {$overdueProjects->count()} project(s).");

        return self::SUCCESS;
    }
}
