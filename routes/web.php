<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PropositionController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/projects', function () {
    return view('allProjects');
})->middleware(['auth', 'verified'])->name('projects');

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
});

require __DIR__.'/auth.php';
