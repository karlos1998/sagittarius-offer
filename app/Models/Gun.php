<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Gun extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'gun_type_id',
        'caliber_id',
        'description',
        'photos',
    ];

    protected $casts = [
        'photos' => 'array',
    ];

    public function gunType(): BelongsTo
    {
        return $this->belongsTo(GunType::class);
    }

    public function caliber(): BelongsTo
    {
        return $this->belongsTo(Caliber::class);
    }
}
