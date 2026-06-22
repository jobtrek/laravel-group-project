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

Route::get('/mail', function() {
    $name = "Thomas Lucking";
    Mail::to('test@test.com')->send(new MailableName($name));
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    Route::get('/create', function () {
        return view('create', ['users' => User::query()->select('id', 'name')->get()]);
    })->name('create');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/propositions', [PropositionController::class, 'store'])->name('proposition.store');
});

require __DIR__.'/auth.php';
