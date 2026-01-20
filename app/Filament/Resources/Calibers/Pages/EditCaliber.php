<?php

namespace App\Filament\Resources\Calibers\Pages;

use App\Filament\Resources\Calibers\CaliberResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditCaliber extends EditRecord
{
    protected static string $resource = CaliberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
