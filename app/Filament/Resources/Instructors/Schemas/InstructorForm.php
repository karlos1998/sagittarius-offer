<?php

namespace App\Filament\Resources\Instructors\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class InstructorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('full_name')
                    ->label('Imię i nazwisko')
                    ->required()
                    ->maxLength(255),
                TextInput::make('sort_order')
                    ->label('Kolejność')
                    ->required()
                    ->integer()
                    ->minValue(0)
                    ->default(0),
                Toggle::make('is_active')
                    ->label('Widoczny na stronie głównej')
                    ->default(true),
                Textarea::make('description')
                    ->label('Opis')
                    ->rows(5)
                    ->columnSpanFull(),
                FileUpload::make('photo')
                    ->label('Zdjęcie')
                    ->image()
                    ->imageEditor()
                    ->imageEditorAspectRatioOptions([
                        '1:1',
                    ])
                    ->imageAspectRatio('1:1')
                    ->automaticallyCropImagesToAspectRatio()
                    ->imageEditorViewportWidth(320)
                    ->imageEditorViewportHeight(320)
                    ->automaticallyResizeImagesToWidth('1200')
                    ->automaticallyResizeImagesToHeight('1200')
                    ->directory('instructors')
                    ->disk(config('filesystems.media_disk', 'public'))
                    ->visibility('public')
                    ->columnSpanFull(),
            ]);
    }
}
