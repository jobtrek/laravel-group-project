<?php

namespace App\Jobs;

use App\Enums\Role;
use App\Mail\StrongerEmailReminder;
use App\Models\Project;
use App\Models\User;
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

        $ccEmails = User::role(Role::ProjectManager->value)->pluck('email');

        Mail::to($leader->email)
            ->cc($ccEmails->all())
            ->send(new StrongerEmailReminder($this->project->title));
    }
}
