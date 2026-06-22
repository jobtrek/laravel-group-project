<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PropositionController;
use App\Mail\MailableName;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/mail', function () {
    $name = 'Thomas Lucking';
    Mail::to('test@test.com')->send(new MailableName($name));
});
Route::get('/dashboard', function () {
    return redirect()->route('projects');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/projects', function () {
    return view('allProjects');
})->middleware(['auth', 'verified'])->name('projects');

Route::get('/propositions', function () {
    return view('propositions');
})->middleware(['auth', 'verified'])->name('propositions');

Route::get('/review', function () {
    return view('review');
})->middleware(['auth', 'verified'])->name('review');

Route::get('/recolte', function () {
    return view('recolte');
})->middleware(['auth', 'verified'])->name('recolte');

Route::get('/en-cours', function () {
    return view('enCours');
})->middleware(['auth', 'verified'])->name('en-cours');

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
