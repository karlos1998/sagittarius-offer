<?php

namespace App\Filament\Resources\Guns\Pages;

use App\Filament\Resources\Guns\GunResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGuns extends ListRecords
{
    protected static string $resource = GunResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
