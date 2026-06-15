<?php

namespace App\Http\Controllers;

use App\Actions\CreateProjectProposal;
use App\Http\Requests\PropositionRequest;
use App\Models\Project;

class PropositionController extends Controller
{
    public function store(PropositionRequest $request, CreateProjectProposal $action)
    {
        $project = $action->execute($request->validated(), auth()->id());
        
        return redirect()->route('dashboard');
    }
}
