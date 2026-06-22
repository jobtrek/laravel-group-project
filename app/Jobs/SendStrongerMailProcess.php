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

        Mail::to($this->project->members)->queue(new StrongerEmailReminder($this->project));

    }
}
