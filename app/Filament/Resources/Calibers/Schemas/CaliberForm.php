<?php

namespace App\Filament\Resources\Calibers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CaliberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nazwa kalibru')
                    ->required(),
            ]);
    }
}
