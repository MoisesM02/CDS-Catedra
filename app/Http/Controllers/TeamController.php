<?php

namespace App\Http\Controllers;

use App\Http\Requests\TeamRequest;
use App\Models\Federation;
use App\Models\Team;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $feds = Federation::get(['id', 'name']);
        $teams = Team::query()
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
            ->with('federation')
            ->paginate(15)
            ->withQueryString(); // Essential: keeps sort/filter in pagination links

        $filters = $request->only('search', 'sort', 'direction');

        return Inertia::render('Team/Index', [
            'teams' => $teams,
            'feds' => $feds,
            'filters' => $filters,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TeamRequest $request)
    {
        Team::create($request->validated());
        return redirect()->back()->with('success', 'Team created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Team $team)
    {
        $team->load(['federation', 'players']);

        return Inertia::render('Team/Show', [
            'team' => $team,
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(TeamRequest $request, Team $team)
    {
        $team->update($request->validated());

        return redirect()->back()->with('success', 'Team updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Team $team)
    {
        $team->delete();

        return redirect()->back()->with('success', 'Team deleted successfully.');
    }
}
