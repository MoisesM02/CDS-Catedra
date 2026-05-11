<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Player extends Model
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    public function teams(): BelongsToMany
    {
        // withPivot() tells Laravel to fetch the extra columns we added to the pivot table
        return $this->belongsToMany(Team::class)
            ->withPivot('start_date', 'end_date')
            ->withTimestamps()
            ->orderByPivot('start_date', 'desc'); // Orders history from newest to oldest
    }

// Optional Helper to get just the current team
    public function currentTeam() : BelongsToMany
    {
        return $this->belongsToMany(Team::class)
            ->withPivot('start_date', 'end_date')
            ->wherePivotNull('end_date')
            ->limit(1);
    }
}
