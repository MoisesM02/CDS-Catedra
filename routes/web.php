<?php

use App\Http\Controllers\FederationController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TeamController;
use App\Models\Federation;
use App\Models\Team;
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
    $teams = Team::count();
    $newTeams = Team::where('created_at', '>', now()->subDays(30))->count();
    $percentage = $newTeams / $teams * 100;

    $teamsData = [
        'count' => $teams,
        'newsCount' => $newTeams,
        'percentage' => number_format($percentage, 2),
    ];

    $federations = Federation::withCount('teams')->where('teams_count','>', 0)->count();
    $newFederations = Federation::where('created_at', '>', now()->subDays(30))->count();
    $percentage = $newFederations / $federations * 100;

    $federationsMostTeams = Federation::withCount('teams')->orderBy('teams_count', 'desc')->take(3)->get();
    $federationsData = [
        'count' => $federations,
        'newsCount' => $newFederations,
        'percentage' => number_format($percentage, 2),
    ];

    return Inertia::render('Dashboard',[
        'teams_data' => $teamsData,
        'federations_data' => $federationsData,
        'federations_most_teams' => $federationsMostTeams,
    ]);
})->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {

    //Federations
    Route::get('/federations', [FederationController::class, 'index'])->name('federation.index');
    Route::get('/federations/edit/{federation}', [FederationController::class, 'edit'])->name('federation.edit');
    Route::post('/federations', [FederationController::class, 'store'])->name('federation.store');
    Route::put('/federations/{federation}', [FederationController::class, 'update'])->name('federation.update');

    //Teams
    Route::get('/teams', [TeamController::class, 'index'])->name('team.index');

    //Players
    Route::get('/players', [PlayerController::class, 'index'])->name('player.index');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
