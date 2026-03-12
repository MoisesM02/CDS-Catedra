<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Team;
use Illuminate\Http\Request;
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
            ->with('team')
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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Player $player)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Player $player)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Player $player)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Player $player)
    {
        //
    }
}
