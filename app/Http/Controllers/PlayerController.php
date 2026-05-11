<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlayerRequest;
use App\Models\Player;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PlayerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $teams = Team::get(['id', 'name']);
        $players = Player::query()
            // 1. Filtering (Search)
            ->when($request->input('search'), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            // 2. Sorting
            ->when($request->input('sort'), function ($query, $sort) use ($request) {
                $direction = $request->input('direction', 'asc');
                $query->orderBy($sort, $direction);
            }, function ($query) {
                $query->orderBy('name', 'asc'); // Default sort
            })
            ->with('currentTeam')
            ->paginate(15)
            ->withQueryString(); // Essential: keeps sort/filter in pagination links

        $filters = $request->only('search', 'sort', 'direction');
        return Inertia::render('Player/Index', [
            'teams' => $teams,
            'players' => $players,
            'filters' => $filters,
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(PlayerRequest $request)
    {
        // 1. Get all validated data
        $validated = $request->validated();

        // 2. Extract the team_id and remove it so it doesn't break Player::create()
        $teamId = $validated['team_id'] ?? null;
        unset($validated['team_id']);

        // 3. Create the player with just name, dob, and gender
        $player = Player::create($validated);

        // 4. If a team was selected in the form, create their first contract record
        if ($teamId) {
            $player->teams()->attach($teamId, [
                'start_date' => Carbon::now()->format('Y-m-d'),
                'end_date' => null, // Active player
            ]);
        }
        return redirect()->back()->with('success', 'Player has been created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Player $player)
    {
        $player->load([
            'currentTeam.federation', // Nested eager loading!
            'teams.federation'        // Load federations for the history table too
        ]);

        return Inertia::render('Player/Show', [
            'player' => $player
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PlayerRequest $request, Player $player)
    {
        // 1. Get all validated data
        $validated = $request->validated();

        // 2. Extract the team_id and remove it from the array
        $newTeamId = $validated['team_id'] ?? null;
        unset($validated['team_id']);

        // 3. Update the basic player info (name, dob, gender)
        $player->update($validated);

        // 4. Handle the Team Transfer Logic
        if (array_key_exists('team_id', $validated) || $newTeamId !== null) {

            $currentTeamId = $player->currentTeam()->first()?->id;

            // If the submitted team is different from their active team, a transfer occurred!
            if ($currentTeamId !== $newTeamId) {

                $today = Carbon::now()->format('Y-m-d');

                // A. "Close" the active contract using the DB facade
                // This explicitly targets the pivot table and avoids the Eloquent routing bug
                DB::table('player_team')
                    ->where('player_id', $player->id)
                    ->whereNull('end_date')
                    ->update(['end_date' => $today]);

                // B. "Open" the new contract
                if ($newTeamId) {
                    $player->teams()->attach($newTeamId, [
                        'start_date' => $today,
                        'end_date' => null,
                    ]);
                }
            }
        }

        return redirect()->back()->with('success', 'Player has been successfully updated.');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Player $player)
    {
        // This removes their current contract AND all historical records
        $player->teams()->detach();
        // 2. Now it is safe to delete the player
        $player->delete();
        return redirect()->back()->with('success', 'Player and their career history have been deleted.');
    }
}
