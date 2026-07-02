<?php

use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\EnCoursController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\PropositionController;
use App\Http\Controllers\RecolteController;
use App\Http\Controllers\ResourceContributionController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\RevisionController;
use App\Models\ProjectPhase;
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
    Route::get('/projects/{project}/direction-review', [ReviewController::class, 'showForm'])->name('projects.direction-review');
    Route::get('/projects/{project}/revision', [RevisionController::class, 'showForm'])->name('projects.revision-form');
    Route::post('/projects/{project}/revision-submit', [RevisionController::class, 'submit'])->name('projects.revision-submit');
});

Route::get('/projects_details/{project}', [ProjectController::class, 'detailPage'])->middleware(['auth', 'verified'])->name('projects-details');

Route::get('/projects_details', function () {
    return view('projectsDetails');
})->middleware(['auth', 'verified'])->name('projects-details');

Route::get('/phase_details/{phase}', function (ProjectPhase $phase) {
    return view('phase_details', compact('phase'));
})->middleware(['auth', 'verified'])->name('phase_details');

Route::middleware('auth')->group(function () {
    Route::get('/create', fn () => view('create', [
        'users' => User::query()->select('id', 'name')->get(),
    ]))->name('create');

    Route::post('/propositions', [PropositionController::class, 'store'])->name('proposition.store');

    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });

    Route::controller(ProjectController::class)->prefix('/projects/{project}')->group(function () {
        Route::patch('/approve', 'approve')
            ->middleware('permission:approve')
            ->name('projects.approve');
        Route::patch('/deny', 'deny')->middleware('permission:deny')->name('projects.deny');
        Route::post('/request-more-info', 'requestMoreInfo')->middleware('permission:review')->name('projects.request-more-info');
        Route::patch('/resubmit', 'reSubmit')->name('projects.resubmit');
        Route::patch('/review', 'review')->middleware('permission:review')->name('projects.review');
    });

    Route::controller(ResourceContributionController::class)->prefix('/projects/{project}/resources')->group(function () {
        Route::get('/create', 'create')->name('projects.resources.create');
        Route::post('/', 'store')->name('projects.resources.store');
    });

    Route::patch('/projects/{project}/move-to-en-cours', [RecolteController::class, 'moveFromRecolteToActive'])
        ->name('projects.recolte.activate');
});

require __DIR__.'/auth.php';
