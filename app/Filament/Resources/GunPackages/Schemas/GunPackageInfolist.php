<?php

namespace App\Filament\Resources\GunPackages\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class GunPackageInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->label('Nazwa pakietu'),
                TextEntry::make('description')
                    ->label('Opis')
                    ->placeholder('-')
                    ->columnSpanFull(),
                ImageEntry::make('preview_image')
                    ->label('Zdjęcie podglądowe')
                    ->disk(config('filesystems.media_disk', 'public'))
                    ->visibility('public')
                    ->height(180)
                    ->placeholder('-')
                    ->columnSpanFull(),
                IconEntry::make('is_active')
                    ->label('Aktywny')
                    ->boolean(),
                TextEntry::make('packageGuns')
                    ->label('Pozycje pakietu')
                    ->state(function ($record): array {
                        return $record->packageGuns
                            ->map(fn ($item): string => sprintf(
                                '%s - %d strzałów (%s)',
                                $item->gun?->name ?? 'Broń usunięta',
                                (int) $item->shots_quantity,
                                $item->ammunition?->name ?? 'Amunicja usunięta'
                            ))
                            ->all();
                    })
                    ->listWithLineBreaks()
                    ->columnSpanFull(),
            ]);
    }
}
