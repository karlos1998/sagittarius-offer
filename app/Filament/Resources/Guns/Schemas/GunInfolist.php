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
                TextEntry::make('photo_urls')
                    ->label('Zdjęcia')
                    ->state(fn ($record): array => $record->photo_urls)
                    ->formatStateUsing(fn ($state): string => collect(is_array($state) ? $state : [$state])
                        ->filter(fn ($url): bool => is_string($url) && $url !== '')
                        ->map(fn (string $url): string => sprintf(
                            '<div style="display: inline-flex; align-items: center; justify-content: center; width: 100%%; max-width: 360px; min-height: 140px; border: 1px solid #d4d4d8; border-radius: 6px; padding: 8px; background: #fff;"><img src="%s" alt="Zdjęcie broni" style="max-width: 100%%; max-height: 180px; width: auto; height: auto; object-fit: contain;" /></div>',
                            e($url)
                        ))
                        ->implode('<br /><br />'))
                    ->html()
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
