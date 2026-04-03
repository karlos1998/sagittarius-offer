<?php

namespace App\Filament\Resources\GunPackages\Pages;

use App\Filament\Resources\GunPackages\GunPackageResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditGunPackage extends EditRecord
{
    protected static string $resource = GunPackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
