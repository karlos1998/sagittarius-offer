<?php

namespace App\Filament\Resources\GunPackages\Pages;

use App\Filament\Resources\GunPackages\GunPackageResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewGunPackage extends ViewRecord
{
    protected static string $resource = GunPackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
