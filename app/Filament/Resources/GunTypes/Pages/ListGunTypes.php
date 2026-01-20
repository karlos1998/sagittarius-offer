<?php

namespace App\Filament\Resources\GunTypes\Pages;

use App\Filament\Resources\GunTypes\GunTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGunTypes extends ListRecords
{
    protected static string $resource = GunTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
