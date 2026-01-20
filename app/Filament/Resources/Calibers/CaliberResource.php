<?php

namespace App\Filament\Resources\Calibers;

use App\Filament\Resources\Calibers\Pages\CreateCaliber;
use App\Filament\Resources\Calibers\Pages\EditCaliber;
use App\Filament\Resources\Calibers\Pages\ListCalibers;
use App\Filament\Resources\Calibers\Pages\ViewCaliber;
use App\Filament\Resources\Calibers\Schemas\CaliberForm;
use App\Filament\Resources\Calibers\Schemas\CaliberInfolist;
use App\Filament\Resources\Calibers\Tables\CalibersTable;
use App\Models\Caliber;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CaliberResource extends Resource
{
    protected static ?string $model = Caliber::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $modelLabel = 'Kaliber';

    protected static ?string $pluralModelLabel = 'Kalibry';

    protected static ?string $navigationLabel = 'Kalibry';

    public static function form(Schema $schema): Schema
    {
        return CaliberForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CaliberInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CalibersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCalibers::route('/'),
            'create' => CreateCaliber::route('/create'),
            'view' => ViewCaliber::route('/{record}'),
            'edit' => EditCaliber::route('/{record}/edit'),
        ];
    }
}
