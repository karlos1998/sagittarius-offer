<?php

namespace App\Filament\Resources\GunTypes\Pages;

use App\Filament\Resources\GunTypes\GunTypeResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditGunType extends EditRecord
{
    protected static string $resource = GunTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
