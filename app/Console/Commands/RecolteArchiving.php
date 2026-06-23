<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use carbon\Carbon;
use App\Models\Project;
use App\Models\States\ArchivedState;
use App\Models\States\CollectingState;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\RecolteArchivingMail;


#[Signature('app:recolte-archiving')]
#[Description('Command description')]
class RecolteArchiving extends Command
{

    protected $signature = 'Recolte:archiving';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // take all projects with status "Collecting" 
        $projects = Project::whereState('status', CollectingState::class)
            ->with('proposer', 'recolteManager')
            ->get();

        foreach ($projects as $project) {
            $dbDate = Carbon::parse($project->updated_at);
            $currentDate = Carbon::now();
            // check if the project has been in "Collecting" status for more than 1 year
            $isOlderThanOneYear = $dbDate->diffInYears($currentDate) >= 1;

            if ($isOlderThanOneYear) {
                // change the status to "Archived"
                $project->status = ArchivedState::class;
                $project->archived_at = Carbon::now();
                $project->save();

                // send an email to the proposer and the recolte manager
                $proposer = $project->proposer;
                $recolteManager = $project->recolteManager;
                if ($recolteManager) {
                    Mail::to($recolteManager->email)->send(new RecolteArchivingMail($recolteManager, $project));
                }

                if ($proposer) {
                    Mail::to($proposer->email)->send(new RecolteArchivingMail($proposer, $project));
                }
            }
        }
    }
}
