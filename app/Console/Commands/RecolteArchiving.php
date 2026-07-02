<?php

namespace App\Console\Commands;

use App\Mail\RecolteArchivingMail;
use App\Models\Project;
use App\Models\States\RecolteState;
use App\Service\ProjectService;
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
        $projects = Project::whereState('status', RecolteState::class)
            ->with('proposer', 'recolteManager')
            ->get();

        foreach ($projects as $project) {
            $isOlderThanOneYear = $project->updated_at->lt(now()->subYear());

            if ($isOlderThanOneYear) {
                ProjectService::archive($project);

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
