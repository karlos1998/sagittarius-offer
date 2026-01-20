<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Gun;

class GunType extends Model
{
    protected $fillable = [
        'name',
    ];

    public function guns(): HasMany
    {
        return $this->hasMany(Gun::class);
    }
}
