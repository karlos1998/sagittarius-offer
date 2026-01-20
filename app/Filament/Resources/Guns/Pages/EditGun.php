<?php

namespace App\Filament\Resources\Guns\Pages;

use App\Filament\Resources\Guns\GunResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditGun extends EditRecord
{
    protected static string $resource = GunResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
