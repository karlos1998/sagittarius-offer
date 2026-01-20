<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Caliber;

class Ammunition extends Model
{
    protected $fillable = [
        'name',
        'caliber_id',
        'club_price',
        'standard_price',
    ];

    protected $casts = [
        'club_price' => 'decimal:2',
        'standard_price' => 'decimal:2',
    ];

    public function caliber(): BelongsTo
    {
        return $this->belongsTo(Caliber::class);
    }
}
