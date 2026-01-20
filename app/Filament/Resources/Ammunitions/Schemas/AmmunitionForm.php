<?php

namespace App\Filament\Resources\Ammunitions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AmmunitionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nazwa amunicji')
                    ->required(),
                Select::make('caliber_id')
                    ->label('Kaliber')
                    ->relationship('caliber', 'name')
                    ->required(),
                TextInput::make('club_price')
                    ->label('Cena klubowa')
                    ->numeric()
                    ->prefix('PLN'),
                TextInput::make('standard_price')
                    ->label('Cena standardowa')
                    ->numeric()
                    ->prefix('PLN'),
            ]);
    }
}
