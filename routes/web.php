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
const FEDERATIONS = "/federations/{federation}";
const TEAMS = "/teams/{team}";
const PLAYERS = "/players/{player}";
const PROFILE = "/profile";


Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

/**
 * This route gets some stats about the federations, clubs and the players
 */
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
    Route::post('/federations', [FederationController::class, 'store'])->name('federation.store');
    Route::get(FEDERATIONS, [FederationController::class, 'show'])->name('federation.show');
    Route::patch(FEDERATIONS, [FederationController::class, 'update'])->name('federation.update');
    Route::delete(FEDERATIONS, [FederationController::class, 'destroy'])->name('federation.destroy');

    //Teams
    Route::get('/teams', [TeamController::class, 'index'])->name('team.index');
    Route::post('/teams', [TeamController::class, 'store'])->name('team.store');
    Route::get(TEAMS, [TeamController::class, 'show'])->name('team.show');
    Route::patch(TEAMS, [TeamController::class, 'update'])->name('team.update');
    Route::delete(TEAMS, [TeamController::class, 'destroy'])->name('team.destroy');

    //Players
    Route::get('/players', [PlayerController::class, 'index'])->name('player.index');
    Route::post('/players', [PlayerController::class, 'store'])->name('player.store');
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
