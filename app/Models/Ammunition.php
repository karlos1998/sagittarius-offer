<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ammunition extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'caliber_id',
        'club_price',
        'standard_price',
        'cart_quantity_step',
    ];

    protected $casts = [
        'club_price' => 'decimal:2',
        'standard_price' => 'decimal:2',
        'cart_quantity_step' => 'integer',
    ];

    public function caliber(): BelongsTo
    {
        return $this->belongsTo(Caliber::class);
    }
}
