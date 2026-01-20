<?php

namespace App\Filament\Resources\Ammunitions\Pages;

use App\Filament\Resources\Ammunitions\AmmunitionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAmmunition extends ViewRecord
{
    protected static string $resource = AmmunitionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
