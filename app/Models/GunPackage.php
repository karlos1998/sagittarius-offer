<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class GunPackage extends Model
{
    /** @use HasFactory<\Database\Factories\GunPackageFactory> */
    use HasFactory;

    protected $appends = [
        'preview_image_url',
    ];

    protected $fillable = [
        'name',
        'description',
        'preview_image',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function guns(): BelongsToMany
    {
        return $this->belongsToMany(Gun::class, 'gun_package_gun')
            ->withPivot('ammunition_id', 'shots_quantity', 'sort_order')
            ->withTimestamps()
            ->orderBy('gun_package_gun.sort_order')
            ->orderBy('guns.name');
    }

    public function packageGuns(): HasMany
    {
        return $this->hasMany(GunPackageGun::class)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function getPreviewImageUrlAttribute(): ?string
    {
        if (! $this->preview_image) {
            return null;
        }

        if (str_starts_with($this->preview_image, 'http://') || str_starts_with($this->preview_image, 'https://')) {
            return $this->preview_image;
        }

        if (! Storage::disk('public')->exists($this->preview_image) && Storage::disk('local')->exists($this->preview_image)) {
            Storage::disk('public')->put($this->preview_image, Storage::disk('local')->get($this->preview_image));
        }

        return '/storage/'.ltrim($this->preview_image, '/');
    }
}
