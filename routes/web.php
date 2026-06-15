<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PropositionController;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use app\Http\Controllers\ProjectController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/create', function () {
    return view('create', ['users' => User::query()->select('id', 'name')->get()]);
})->middleware(['auth', 'verified'])->name('create');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/propositions', [PropositionController::class, 'store'])->name('proposition.store');
});

require __DIR__ . '/auth.php';
