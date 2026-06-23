<?php

namespace App\Jobs;

use App\Mail\StrongerEmailReminder;
use App\Models\Project;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendStrongerMailProcess implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Project $project

    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $leader = $this->project->leader;
        if (! $leader) {
            return;
        }

        $ccEmails = $this->project->members->pluck('email');
        if ($this->project->recolteManager) {
            $ccEmails->push($this->project->recolteManager->email);
        }

        Mail::to($leader->email)
            ->cc($ccEmails->all())
            ->send(new StrongerEmailReminder($this->project->title));
    }
}
