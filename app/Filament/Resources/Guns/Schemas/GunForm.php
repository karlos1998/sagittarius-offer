<?php

namespace App\Filament\Resources\Guns\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class GunForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nazwa broni')
                    ->required(),
                Select::make('gun_type_id')
                    ->label('Typ broni')
                    ->relationship('gunType', 'name')
                    ->required(),
                Select::make('caliber_id')
                    ->label('Kaliber')
                    ->relationship('caliber', 'name')
                    ->required(),
                Textarea::make('description')
                    ->label('Opis')
                    ->columnSpanFull(),
                FileUpload::make('photos')
                    ->label('Zdjęcia')
                    ->multiple()
                    ->maxFiles(10)
                    ->maxSize(20480) // 20MB in KB
                    ->image()
                    ->directory('guns')
                    ->columnSpanFull(),
            ]);
    }
}
