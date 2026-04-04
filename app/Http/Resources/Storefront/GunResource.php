<?php

namespace App\Http\Resources\Storefront;

use App\Support\MediaUrlResolver;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GunResource extends JsonResource
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
            'photos' => $this->photos ?? [],
            'photo_urls' => $mediaUrlResolver->many($this->photos),
            'gun_type_id' => $this->gun_type_id,
            'gun_type' => $this->whenLoaded('gunType', fn (): ?array => $this->gunType ? [
                'id' => $this->gunType->id,
                'name' => $this->gunType->name,
            ] : null),
            'caliber' => $this->whenLoaded('caliber', fn (): ?array => $this->caliber ? [
                'id' => $this->caliber->id,
                'name' => $this->caliber->name,
                'ammunitions' => $this->caliber->ammunitions
                    ->map(fn ($ammunition): array => [
                        'id' => $ammunition->id,
                        'name' => $ammunition->name,
                        'standard_price' => $ammunition->standard_price,
                        'club_price' => $ammunition->club_price,
                    ])
                    ->values()
                    ->all(),
            ] : null),
        ];
    }
}
