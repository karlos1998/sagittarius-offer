<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GunPackageGun extends Model
{
    use HasFactory;

    protected $table = 'gun_package_gun';

    protected $fillable = [
        'gun_package_id',
        'gun_id',
        'ammunition_id',
        'shots_quantity',
        'sort_order',
    ];

    public function gunPackage(): BelongsTo
    {
        return $this->belongsTo(GunPackage::class);
    }

    public function gun(): BelongsTo
    {
        return $this->belongsTo(Gun::class);
    }

    public function ammunition(): BelongsTo
    {
        return $this->belongsTo(Ammunition::class);
    }
}
