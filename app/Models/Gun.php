<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

class Gun extends Model
{
    use HasFactory;

    protected $appends = [
        'photo_urls',
    ];

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

    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(GunPackage::class, 'gun_package_gun')
            ->withPivot('ammunition_id', 'shots_quantity', 'sort_order')
            ->withTimestamps();
    }

    /**
     * @return array<int, string>
     */
    public function getPhotoUrlsAttribute(): array
    {
        return collect($this->photos ?? [])
            ->filter(fn ($path): bool => is_string($path) && $path !== '')
            ->map(function (string $path): string {
                if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
                    return $path;
                }

                if (! Storage::disk('public')->exists($path) && Storage::disk('local')->exists($path)) {
                    Storage::disk('public')->put($path, Storage::disk('local')->get($path));
                }

                return '/storage/'.ltrim($path, '/');
            })
            ->values()
            ->all();
    }
}
