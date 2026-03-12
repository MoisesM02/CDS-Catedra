<?php

use App\Http\Controllers\FederationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TeamController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {

    //Federations
    Route::get('/federations', [FederationController::class, 'index'])->name('federation.index');
    Route::get('/federations/edit/{federation}', [FederationController::class, 'edit'])->name('federation.edit');
    Route::post('/federations', [FederationController::class, 'store'])->name('federation.store');
    Route::put('/federations/{federation}', [FederationController::class, 'update'])->name('federation.update');

    //Teams
    Route::get('/teams', [TeamController::class, 'index'])->name('team.index');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
