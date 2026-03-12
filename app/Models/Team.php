<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [];

    public function federation(): BelongsTo
    {
        return $this->belongsTo(Federation::class);
    }

    public function players(): HasMany
    {
        return $this->hasMany(Player::class);
    }
}
