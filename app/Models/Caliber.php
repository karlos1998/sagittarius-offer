<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Caliber extends Model
{
    use HasFactory;

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
