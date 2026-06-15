<?php

namespace App\Http\Controllers;

use App\Http\Requests\PropositionRequest;
use App\Models\Project;

class PropositionController extends Controller
{
    public function store(PropositionRequest $request)
    {

        Project::createProposal($request->validated(), auth()->id());

        return redirect()->route('dashboard');
    }
}
