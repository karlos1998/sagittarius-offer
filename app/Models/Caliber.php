<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Gun;
use App\Models\Ammunition;

class Caliber extends Model
{
    protected $fillable = [
        'name',
    ];

    public function guns(): HasMany
    {
        return $this->hasMany(Gun::class);
    }

    public function ammunitions(): HasMany
    {
        return $this->hasMany(Ammunition::class);
    }
}
