<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class MediaUrlResolver
{
    public function __construct(
        private readonly string $disk = 'public'
    ) {}

    public static function make(): self
    {
        return new self((string) config('filesystems.media_disk', 'public'));
    }

    public function url(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return Storage::disk($this->disk)->url($path);
    }

    /**
     * @param  array<int, mixed>|null  $paths
     * @return array<int, string>
     */
    public function many(?array $paths): array
    {
        return collect($paths ?? [])
            ->filter(fn ($path): bool => is_string($path) && $path !== '')
            ->map(fn (string $path): ?string => $this->url($path))
            ->filter()
            ->values()
            ->all();
    }
}
