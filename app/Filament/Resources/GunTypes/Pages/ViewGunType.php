<?php

namespace App\Filament\Resources\GunTypes\Pages;

use App\Filament\Resources\GunTypes\GunTypeResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewGunType extends ViewRecord
{
    protected static string $resource = GunTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
