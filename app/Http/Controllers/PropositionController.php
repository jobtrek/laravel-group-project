<?php

namespace App\Http\Controllers;

use App\Actions\CreateProjectProposal;
use App\Enums\Stage;
use App\Http\Requests\PropositionRequest;
use App\Models\States\PropositionState;
use App\Models\States\RevisionState;
use Illuminate\Database\Eloquent\Builder;

class PropositionController extends StageProjectController
{
    protected function stage(): Stage
    {
        return Stage::Propositions;
    }

    protected function states(): string|array
    {
        return [PropositionState::class, RevisionState::class];
    }

    protected function baseQuery(): Builder
    {

        if (request()->input('proposer_id') === 'all') {
            return parent::baseQuery();
        }

        return parent::baseQuery()
            ->where('proposer_id', auth()->id());
    }

    protected function myProposals(): bool
    {
        return true;
    }

    public function store(PropositionRequest $request, CreateProjectProposal $action)
    {
        $action->execute($request->validated(), auth()->id());

        return redirect()->route('projects')->with('success', "Le projet a été proposé");
    }
}
