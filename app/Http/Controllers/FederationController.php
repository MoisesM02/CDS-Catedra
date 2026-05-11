<?php

namespace App\Http\Controllers;

use App\Models\Federation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class FederationController extends Controller
{
    /**
     * Shows the federations and filters them accordingly.
     * @param Request $request
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $feds = Federation::query()
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
            ->paginate(15)
            ->withQueryString(); // Essential: keeps sort/filter in pagination links

        $filters = $request->only('search', 'sort', 'direction');

        return Inertia::render('Federation/Index', [
            'feds' => $feds,
            'filters' => $filters,
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $federationAttributes = $request->validate([
            'name' => ['required', 'string', 'unique:federations,name'],
            'address' => ['required', 'string'],
            'date_of_foundation' => ['required', 'date', 'date_format:Y-m-d', 'before_or_equal:today'],
            'logo' => ['required', 'url'],
        ]);
        Federation::create($federationAttributes);
        return redirect()->back()->with('success', 'Federation created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Federation $federation)
    {
        // Eager load the teams and count their active players
        $federation->load(['teams' => function ($query) {
            // alias the count as 'active_players_count'
            // filter it to only count players where end_date is null
            $query->withCount(['players as active_players_count' => function ($q) {
                $q->whereNull('player_team.end_date');
            }]);
        }]);

        //  Calculate Top-Level Federation Statistics
        $totalTeams = $federation->teams->count();
        $totalActivePlayers = $federation->teams->sum('active_players_count');

        return Inertia::render('Federation/Show', [
            'federation' => $federation,
            'stats' => [
                'total_teams' => $totalTeams,
                'total_active_players' => $totalActivePlayers,
            ]
        ]);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Federation $federation)
    {

        $federationAttributes = $request->validate([
            'name' => ['required', 'string', Rule::unique('federations', 'name')->ignore($federation->id)],
            'address' => ['required', 'string'],
            'date_of_foundation' => ['required', 'date', 'date_format:Y-m-d'],
            'logo' => ['required', 'url'],
        ]);
        $federation->update($federationAttributes);
        return redirect()->back()->with('success', 'Federation updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Federation $federation)
    {
        $federation->delete();
        return redirect()->back()->with('success', 'Federation deleted successfully.');
    }
}
