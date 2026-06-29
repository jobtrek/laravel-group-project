<?php

use App\Enums\Stage;
use App\Http\Controllers\EnCoursController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\PropositionController;
use App\Http\Controllers\RecolteController;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\ReviewController;
use App\Models\Project;
use App\Models\States\EvaluationState;
use App\Models\States\PropositionState;
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

Route::get('/propositions', [PropositionController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('propositions');

Route::get('/evaluation', [ReviewController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('evaluation');

Route::get('/recolte', [RecolteController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('recolte');

Route::get('/en-cours', [EnCoursController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('en-cours');

Route::get('/frigo', [ArchiveController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('frigo');

Route::get('/projects_details', function () {
    return view('projectsDetails');
})->middleware(['auth', 'verified'])->name('projects-details');

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
            ->whereState('status', [PropositionState::class, EvaluationState::class])
            ->get();
        return view('testDirectionFront', ['projects' => $projects]);
    })->name('direction.projects');

    Route::patch('/projects/{project}/approve', [ProjectController::class, 'approve'])->name('projects.approve');
    Route::patch('/projects/{project}/deny', [ProjectController::class, 'deny'])->name('projects.deny');
    Route::post('/projects/{project}/request-more-info', [ProjectController::class, 'requestMoreInfo'])->name('projects.request-more-info');
    Route::patch('/projects/{project}/resubmit', [ProjectController::class, 'reSubmit'])->name('projects.resubmit');
});

require __DIR__.'/auth.php';