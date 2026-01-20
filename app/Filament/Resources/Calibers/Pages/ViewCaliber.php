<?php

namespace App\Filament\Resources\Calibers\Pages;

use App\Filament\Resources\Calibers\CaliberResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCaliber extends ViewRecord
{
    protected static string $resource = CaliberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
