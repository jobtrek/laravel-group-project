<?php

use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CompleteController;
use App\Http\Controllers\EnCoursController;
use App\Http\Controllers\PhaseItemCompletionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\PropositionController;
use App\Http\Controllers\RecolteController;
use App\Http\Controllers\ResourceContributionController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\RevisionController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('auth.login'))->middleware('guest');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', fn () => redirect()->route('projects'))->name('dashboard');
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects');
    Route::get('/propositions', [PropositionController::class, 'index'])->name('propositions');
    Route::get('/evaluation', [ReviewController::class, 'index'])->name('evaluation');
    Route::get('/recolte', [RecolteController::class, 'index'])->name('recolte');
    Route::get('/en-cours', [EnCoursController::class, 'index'])->name('en-cours');
    Route::get('/complete', [CompleteController::class, 'index'])->name('complete');
    Route::get('/frigo', [ArchiveController::class, 'index'])->name('frigo');
    Route::get('/projects/{project}/direction-review', [ReviewController::class, 'showForm'])->name('projects.direction-review');
    Route::get('/projects/{project}/revision', [RevisionController::class, 'showForm'])->name('projects.revision-form');
    Route::post('/projects/{project}/revision-submit', [RevisionController::class, 'submit'])->name('projects.revision-submit');
});

Route::get('/projects_details/{project}', [ProjectController::class, 'detailPage'])->middleware(['auth', 'verified'])->name('projects-details');

Route::get('/projects_details/{project}/phase_details/{phase}', [ProjectController::class, 'phaseDetail'])->middleware(['auth', 'verified'])->name('phase_details')->scopeBindings();
Route::patch(
    '/projects_details/{project}/phase_details/{phase}/items/{itemType}/{itemIndex}',
    [PhaseItemCompletionController::class, 'toggle']
)
    ->whereIn('itemType', ['objectif', 'livrable'])
    ->whereNumber('itemIndex')
    ->middleware(['auth', 'verified', 'can:edit project'])
    ->name('phase_details.items.toggle')
    ->scopeBindings();

Route::middleware('auth')->group(function () {
    Route::get('/create', fn () => view('create'))->name('create');

    Route::post('/propositions', [PropositionController::class, 'store'])->name('proposition.store');

    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
    });

    Route::controller(ProjectController::class)->prefix('/projects/{project}')->group(function () {
        Route::patch('/approve', 'approve')
            ->middleware('can:approve')
            ->name('projects.approve');
        Route::patch('/deny', 'deny')->middleware('can:deny')->name('projects.deny');
        Route::post('/request-more-info', 'requestMoreInfo')->middleware('can:review')->name('projects.request-more-info');
        Route::patch('/resubmit', 'reSubmit')->name('projects.resubmit');
        Route::get('/edit', 'edit')->name('projects.edit');
        Route::patch('/', 'update')->name('projects.update');
        Route::post('/comments', [CommentController::class, 'store'])
            ->name('projects.comments.store');
        Route::patch('/complete', 'complete')->middleware('can:complete project')->name('projects.complete');
        Route::patch('/archive', 'archive')->middleware('role:admin')->name('projects.archive');
        Route::patch('/send-to-direction', 'sendToDirection')->middleware('can:send to direction')->name('projects.send-to-direction');
    });

    Route::controller(ResourceContributionController::class)->prefix('/projects/{project}/resources')
        ->middleware('can:add resources')->
        group(function () {
            Route::get('/create', 'create')->name('projects.resources.create');
            Route::post('/', 'store')->name('projects.resources.store');
        });

    Route::patch('/projects/{project}/move-to-en-cours', [RecolteController::class, 'moveFromRecolteToActive'])
        ->middleware(['can:launch project', 'project.has-leader'])
        ->name('projects.recolte.activate');

    Route::patch('/projects/{project}/team', [RecolteController::class, 'assignTeam'])
        ->middleware('can:assign team')
        ->name('projects.recolte.team');
});

require __DIR__.'/auth.php';
