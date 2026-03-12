<?php

namespace App\Http\Controllers;

use App\Models\Federation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class FederationController extends Controller
{
    /**
     * Display a listing of the resource.
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
        $federationAttributes = $request->validate([
            'name' => ['required', 'string', 'unique:federations,name'],
            'address' => ['required', 'string'],
            'date_of_foundation' => ['required', 'date', 'date_format:Y-m-d'],
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Federation $federation)
    {
        return Inertia::render('Federation/Edit', [
            'federation' => $federation,
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
        //
    }
}
