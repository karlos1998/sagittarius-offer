<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    /** @use HasFactory<\Database\Factories\OrderItemFactory> */
    use HasFactory;

    protected $fillable = [
        'order_id',
        'gun_id',
        'ammunition_id',
        'gun_package_id',
        'gun_name',
        'ammunition_name',
        'gun_package_name',
        'gun_package_guns_summary',
        'quantity',
        'price_per_shot',
        'line_total',
    ];

    protected function casts(): array
    {
        return [
            'gun_package_id' => 'integer',
            'price_per_shot' => 'decimal:2',
            'line_total' => 'decimal:2',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function gun(): BelongsTo
    {
        return $this->belongsTo(Gun::class);
    }

    public function ammunition(): BelongsTo
    {
        return $this->belongsTo(Ammunition::class);
    }

    public function gunPackage(): BelongsTo
    {
        return $this->belongsTo(GunPackage::class);
    }
}
