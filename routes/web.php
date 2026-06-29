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

Route::get('/', fn () => view('welcome'));

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', fn () => redirect()->route('projects'))->name('dashboard');
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects');
    Route::get('/propositions', [PropositionController::class, 'index'])->name('propositions');
    Route::get('/evaluation', [ReviewController::class, 'index'])->name('evaluation');
    Route::get('/recolte', [RecolteController::class, 'index'])->name('recolte');
    Route::get('/en-cours', [EnCoursController::class, 'index'])->name('en-cours');
    Route::get('/frigo', [ArchiveController::class, 'index'])->name('frigo');
});

Route::middleware('auth')->group(function () {
    Route::get('/create', fn () => view('create', [
        'users' => User::query()->select('id', 'name')->get(),
    ]))->name('create');

    Route::get('/direction/projects', function () {
        $projects = Project::with('evaluation')
            ->whereState('status', [PropositionState::class, EvaluationState::class])
            ->get();
        return view('testDirectionFront', ['projects' => $projects]);
    })->name('direction.projects');

    Route::post('/propositions', [PropositionController::class, 'store'])->name('proposition.store');

    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });

    Route::controller(ProjectController::class)->prefix('/projects/{project}')->group(function () {
        Route::patch('/approve', 'approve')->name('projects.approve');
        Route::patch('/deny', 'deny')->name('projects.deny');
        Route::post('/request-more-info', 'requestMoreInfo')->name('projects.request-more-info');
        Route::patch('/resubmit', 'reSubmit')->name('projects.resubmit');
    });
});

require __DIR__.'/auth.php';