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
//Defining constants
// Defining constants safely for testing
if (!defined('FEDERATIONS')){
    define('FEDERATIONS', '/federations/{federation}');
}
if (!defined('TEAMS')) {
    define('TEAMS', '/teams/{team}');
}
if (!defined('PLAYERS')) {
    define('PLAYERS', '/players/{player}');
}
if (!defined('PROFILE')) {
    define('PROFILE', '/profile');
}


Route::get('/', function () {
    return redirect('/dashboard');
});

/**
 * This route gets some stats about the federations, clubs and the players
 */
Route::get('/dashboard', function () {
    // 1. Teams Data
    $teams = Team::count();
    $newTeams = Team::where('created_at', '>', now()->subDays(30))->count();

// The Fix: Check if $teams > 0 before dividing
    $teamsPercentage = $teams > 0 ? ($newTeams / $teams * 100) : 0;

    $teamsData = [
        'count' => $teams,
        'newsCount' => $newTeams,
        'percentage' => number_format($teamsPercentage, 2),
    ];


// 2. Federations Data
// Refactored to use has() which is faster and cleaner than withCount()->where()
    $federations = Federation::has('teams')->count();
    $newFederations = Federation::where('created_at', '>', now()->subDays(30))->count();

// The Fix: Check if $federations > 0 before dividing
    $federationsPercentage = $federations > 0 ? ($newFederations / $federations * 100) : 0;

    $federationsMostTeams = Federation::withCount('teams')
        ->orderByDesc('teams_count')
        ->take(3)
        ->get();

    $federationsData = [
        'count' => $federations,
        'newsCount' => $newFederations,
        'percentage' => number_format($federationsPercentage, 2),
    ];

    return Inertia::render('Dashboard', [
        'teams_data' => $teamsData,
        'federations_data' => $federationsData,
        'federations_most_teams' => $federationsMostTeams,
    ]);
})->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {

    //Federations
    Route::get('/federations', [FederationController::class, 'index'])->name('federation.index');
    Route::post('/federations', [FederationController::class, 'store'])->name('federation.store');
    Route::get('/federations/{federation}', [FederationController::class, 'show'])->name('federation.show');
    Route::get(FEDERATIONS, [FederationController::class, 'show'])->name('federation.show');
    Route::patch(FEDERATIONS, [FederationController::class, 'update'])->name('federation.update');
    Route::delete(FEDERATIONS, [FederationController::class, 'destroy'])->name('federation.destroy');

    //Teams
    Route::get('/teams', [TeamController::class, 'index'])->name('team.index');
    Route::post('/teams', [TeamController::class, 'store'])->name('team.store');
    Route::get('teams/{team}', [TeamController::class, 'show'])->name('team.show');
    Route::get(TEAMS, [TeamController::class, 'show'])->name('team.show');
    Route::patch(TEAMS, [TeamController::class, 'update'])->name('team.update');
    Route::delete(TEAMS, [TeamController::class, 'destroy'])->name('team.destroy');

    //Players
    Route::get('/players', [PlayerController::class, 'index'])->name('player.index');
    Route::post('/players', [PlayerController::class, 'store'])->name('player.store');
    Route::get('/players/{player}', [PlayerController::class, 'show'])->name('player.show');
    Route::get(PLAYERS, [PlayerController::class, 'show'])->name('player.show');
    Route::patch(PLAYERS, [PlayerController::class, 'update'])->name('player.update');
    Route::delete(PLAYERS, [PlayerController::class, 'destroy'])->name('player.destroy');
});


Route::middleware('auth')->group(function () {
    Route::get(PROFILE, [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch(PROFILE, [ProfileController::class, 'update'])->name('profile.update');
    Route::delete(PROFILE, [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
