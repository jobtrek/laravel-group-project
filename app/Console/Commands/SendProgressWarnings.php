<?php

namespace App\Console\Commands;

use App\Jobs\SendStrongerMailProcess;
use App\Models\Project;
use App\Models\States\EncoursState;
use Illuminate\Console\Command;

class SendProgressWarnings extends Command
{
    protected $signature = 'mail:send-warnings';

    protected $description = 'Queue firm warning emails for projects overdue for a progress update';

    public function handle(): int
    {
        $overdueProjects = Project::with('members')
            ->whereState('status', EncoursState::class)
            ->whereNotNull('last_reminder_at')
            ->where('last_reminder_at', '<', now()->subWeeks((int) config('projects.escalation_after_weeks', 1)))
            ->whereColumn('updated_at', '<', 'last_reminder_at')
            ->get();

        foreach ($overdueProjects as $project) {
            SendStrongerMailProcess::dispatch($project);
            $project->timestamps = false;
            $project->forceFill(['last_reminder_at' => now()])->saveQuietly();
        }

        $this->info("Warning emails queued for {$overdueProjects->count()} project(s).");

        return self::SUCCESS;
    }
}
