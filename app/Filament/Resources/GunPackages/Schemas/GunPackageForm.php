<?php

namespace App\Filament\Resources\GunPackages\Schemas;

use App\Models\Gun;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class GunPackageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nazwa pakietu')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->label('Opis pakietu')
                    ->rows(3)
                    ->columnSpanFull(),
                FileUpload::make('preview_image')
                    ->label('Zdjęcie podglądowe pakietu')
                    ->image()
                    ->directory('gun-packages')
                    ->disk(config('filesystems.media_disk', 'public'))
                    ->visibility('public')
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->label('Pakiet aktywny')
                    ->default(true),
                Repeater::make('packageGuns')
                    ->label('Pozycje pakietu')
                    ->relationship('packageGuns')
                    ->orderColumn('sort_order')
                    ->defaultItems(1)
                    ->columnSpanFull()
                    ->schema([
                        Select::make('gun_id')
                            ->label('Broń')
                            ->relationship('gun', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->live(),
                        Select::make('ammunition_id')
                            ->label('Amunicja')
                            ->required()
                            ->searchable()
                            ->options(function (Get $get): array {
                                $gunId = (int) ($get('gun_id') ?? 0);

                                if ($gunId === 0) {
                                    return [];
                                }

                                $gun = Gun::query()
                                    ->with('caliber.ammunitions')
                                    ->find($gunId);

                                if (! $gun || ! $gun->caliber) {
                                    return [];
                                }

                                return $gun->caliber->ammunitions
                                    ->pluck('name', 'id')
                                    ->toArray();
                            }),
                        TextInput::make('shots_quantity')
                            ->label('Liczba strzałów')
                            ->required()
                            ->integer()
                            ->minValue(1)
                            ->default(5)
                            ->suffix('strzałów'),
                    ]),
            ]);
    }
}
