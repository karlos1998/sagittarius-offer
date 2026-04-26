<?php

namespace App\Filament\Resources\Instructors\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class InstructorInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('full_name')
                    ->label('Imię i nazwisko'),
                IconEntry::make('is_active')
                    ->label('Widoczny')
                    ->boolean(),
                TextEntry::make('sort_order')
                    ->label('Kolejność'),
                TextEntry::make('description')
                    ->label('Opis')
                    ->placeholder('-')
                    ->columnSpanFull(),
                ImageEntry::make('photo')
                    ->label('Zdjęcie')
                    ->disk(config('filesystems.media_disk', 'public'))
                    ->visibility('public')
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
