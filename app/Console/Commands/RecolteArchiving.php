<?php

namespace App\Console\Commands;

use App\Mail\RecolteArchivingMail;
use App\Models\Project;
use App\Models\States\ArchiveState;
use App\Models\States\RecolteState;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class RecolteArchiving extends Command
{
    protected $signature = 'recolte:archiving';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // take all projects with status "Collecting"
        $projects = Project::whereState('status', RecolteState::class)
            ->with('proposer', 'recolteManager')
            ->get();

        foreach ($projects as $project) {
            // check if the project has been in "Collecting" status for more than 1 year
            $isOlderThanOneYear = $project->updated_at->lt(now()->subYear());

            if ($isOlderThanOneYear) {
                // change the status to "Archived"
                $project->status->transitionTo(ArchiveState::class);
                $project->archived_at = Carbon::now();
                $project->save();

                // send an email to the proposer and the recolte manager
                $proposer = $project->proposer;
                $recolteManager = $project->recolteManager;
                if ($recolteManager && $recolteManager->email !== $proposer->email) {
                    Mail::to($recolteManager->email)->send(new RecolteArchivingMail($recolteManager, $project));
                }

                if ($proposer) {
                    Mail::to($proposer->email)->send(new RecolteArchivingMail($proposer, $project));
                }
            }
        }
    }
}
