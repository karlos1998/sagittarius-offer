<?php

namespace App\Filament\Resources\GunPackages\Pages;

use App\Filament\Resources\GunPackages\GunPackageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGunPackages extends ListRecords
{
    protected static string $resource = GunPackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
