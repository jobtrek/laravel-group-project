<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ToggleItemCompletionRequest;
use App\Models\Project;
use App\Models\ProjectPhase;
use Illuminate\Http\RedirectResponse;

class PhaseItemCompletionController extends Controller
{
    public function toggle(
        ToggleItemCompletionRequest $request,
        Project $project,
        ProjectPhase $phase,
        string $itemType,
        string $itemIndex,
    ): RedirectResponse {
        $itemIndex = (int) $itemIndex;

        $items = match ($itemType) {
            'objectif' => $phase->objectifs ?? [],
            'livrable' => $phase->livrables ?? [],
        };

        abort_if($itemIndex < 0 || $itemIndex >= count($items), 404);
        abort_if((string) $project->status !== 'en cours', 403);

        $completed = $request->boolean('completed');

        $phase->itemCompletions()->updateOrCreate(
            ['item_type' => $itemType, 'item_index' => $itemIndex],
            [
                'completed' => $completed,
                'completed_by' => $completed ? auth()->id() : null,
                'completed_at' => $completed ? now() : null,
            ],
        );

        return redirect()->back();
    }
}
