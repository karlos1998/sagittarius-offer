<?php

namespace App\Filament\Resources\Guns\Pages;

use App\Filament\Resources\Guns\GunResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewGun extends ViewRecord
{
    protected static string $resource = GunResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
