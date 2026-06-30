<?php declare(strict_types=1);

namespace App\Actions;

use App\Mail\RequestMoreInfoMail;
use App\Models\Comment;
use App\Models\Project;
use App\Models\States\RevisionState;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class RequestMoreInfoAction
{
    /**
     * @param  array<string, string>  $fieldComments  e.g. ['title' => 'Préciser ceci']
     */
    public function execute(
        Project $project,
        array $fieldComments,
        int $directionUserId,
    ): void {
        DB::transaction(function () use ($project, $fieldComments, $directionUserId): void {
            foreach ($fieldComments as $fieldKey => $content) {
                Comment::create([
                    'content'    => $content,
                    'field_key'  => $fieldKey,
                    'stage'      => 'review',
                    'user_id'    => $directionUserId,
                    'project_id' => $project->id,
                ]);
            }

            $project->status->transitionTo(RevisionState::class);
        });

        // After the DB transaction commits successfully, queue the notification.
        Mail::to($project->proposer)->queue(new RequestMoreInfoMail($project));
    }
}