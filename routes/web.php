<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PropositionController;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use app\Http\Controllers\ProjectController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [ProjectController::class, 'listingProjects'])->middleware(['auth', 'verified'])->name('dashboard');
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/propositions', [PropositionController::class, 'store'])->name('proposition.store');
});

require __DIR__ . '/auth.php';
