<?php

namespace App\Http\Resources\Storefront;

use App\Support\MediaUrlResolver;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GunPackageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $mediaUrlResolver = MediaUrlResolver::make();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'preview_image' => $this->preview_image,
            'preview_image_url' => $mediaUrlResolver->url($this->preview_image),
            'is_active' => (bool) $this->is_active,
            'package_guns' => $this->whenLoaded('packageGuns', fn (): array => $this->packageGuns
                ->map(fn ($packageGun): array => [
                    'id' => $packageGun->id,
                    'gun_id' => $packageGun->gun_id,
                    'ammunition_id' => $packageGun->ammunition_id,
                    'shots_quantity' => (int) $packageGun->shots_quantity,
                    'sort_order' => (int) $packageGun->sort_order,
                    'gun' => $packageGun->gun ? [
                        'id' => $packageGun->gun->id,
                        'name' => $packageGun->gun->name,
                    ] : null,
                    'ammunition' => $packageGun->ammunition ? [
                        'id' => $packageGun->ammunition->id,
                        'name' => $packageGun->ammunition->name,
                    ] : null,
                ])
                ->values()
                ->all()),
        ];
    }
}
