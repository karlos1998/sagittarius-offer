<?php

namespace App\Filament\Resources\Guns\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class GunInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('gunType.name')
                    ->label('Gun type'),
                TextEntry::make('caliber.name')
                    ->label('Caliber'),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('photos')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
