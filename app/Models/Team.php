<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    public function federation(): BelongsTo
    {
        return $this->belongsTo(Federation::class);
    }

    // 1. ALL Players (Current and Historical)
    public function players() : BelongsToMany
    {
        return $this->belongsToMany(Player::class)
            ->withPivot('start_date', 'end_date')
            ->orderByPivot('start_date', 'desc')
            ->withTimestamps();
    }

    // 2. ONLY Current Players (Active Roster)
    public function currentPlayers() : BelongsToMany
    {
        return $this->belongsToMany(Player::class)
            ->withPivot('start_date', 'end_date')
            ->wherePivotNull('end_date') // Only records where end_date is NULL
            ->orderByPivot('start_date', 'desc')
            ->withTimestamps();
    }

    // 3. ONLY Past Players (Alumni/Transferred)
    public function pastPlayers() : BelongsToMany
    {
        return $this->belongsToMany(Player::class)
            ->withPivot('start_date', 'end_date')
            ->wherePivotNotNull('end_date') // Only records where end_date has a value
            ->orderByPivot('end_date', 'desc')
            ->withTimestamps();
    }
}
