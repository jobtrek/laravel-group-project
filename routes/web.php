<?php

use App\Enums\Stage;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\PropositionController;
use App\Models\Project;
use App\Models\States\EvaluationState;
use App\Models\States\SubmittedState;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return redirect()->route('projects');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/projects', [ProjectController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('projects');

Route::get('/propositions', [ProjectController::class, 'stage'])
    ->defaults('stage', Stage::Propositions)
    ->middleware(['auth', 'verified'])->name('propositions');

Route::get('/review', [ProjectController::class, 'stage'])
    ->defaults('stage', Stage::Review)
    ->middleware(['auth', 'verified'])->name('review');

Route::get('/recolte', [ProjectController::class, 'stage'])
    ->defaults('stage', Stage::Recolte)
    ->middleware(['auth', 'verified'])->name('recolte');

Route::get('/en-cours', [ProjectController::class, 'stage'])
    ->defaults('stage', Stage::EnCours)
    ->middleware(['auth', 'verified'])->name('en-cours');

Route::get('/archive', [ProjectController::class, 'stage'])
    ->defaults('stage', Stage::Archive)
    ->middleware(['auth', 'verified'])->name('archive');

Route::middleware('auth')->group(function () {
    Route::get('/create', function () {
        return view('create', ['users' => User::query()->select('id', 'name')->get()]);
    })->name('create');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/propositions', [PropositionController::class, 'store'])->name('proposition.store');

    Route::get('/direction/projects', function () {
        $projects = Project::with('evaluation')
            ->whereState('status', [SubmittedState::class, EvaluationState::class])
            ->get();

        return view('testDirectionFront', ['projects' => $projects]);
    })->name('direction.projects');

    Route::patch('/projects/{project}/approve', [ProjectController::class, 'approve'])->name('projects.approve');
    Route::patch('/projects/{project}/deny', [ProjectController::class, 'deny'])->name('projects.deny');
    Route::post('/projects/{project}/request-more-info', [ProjectController::class, 'requestMoreInfo'])->name('projects.request-more-info');
    Route::patch('/projects/{project}/resubmit', [ProjectController::class, 'reSubmit'])->name('projects.resubmit');
});

require __DIR__ . '/auth.php';
