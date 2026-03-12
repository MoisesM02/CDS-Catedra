<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Federation extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'address',
        'date_of_foundation',
        'logo'
    ];

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }
}
