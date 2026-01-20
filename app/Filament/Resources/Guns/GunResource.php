<?php

namespace App\Filament\Resources\Guns;

use App\Filament\Resources\Guns\Pages\CreateGun;
use App\Filament\Resources\Guns\Pages\EditGun;
use App\Filament\Resources\Guns\Pages\ListGuns;
use App\Filament\Resources\Guns\Pages\ViewGun;
use App\Filament\Resources\Guns\Schemas\GunForm;
use App\Filament\Resources\Guns\Schemas\GunInfolist;
use App\Filament\Resources\Guns\Tables\GunsTable;
use App\Models\Gun;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GunResource extends Resource
{
    protected static ?string $model = Gun::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $modelLabel = 'Broń';

    protected static ?string $pluralModelLabel = 'Broń';

    protected static ?string $navigationLabel = 'Broń';

    public static function form(Schema $schema): Schema
    {
        return GunForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return GunInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GunsTable::configure($table);
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
            'index' => ListGuns::route('/'),
            'create' => CreateGun::route('/create'),
            'view' => ViewGun::route('/{record}'),
            'edit' => EditGun::route('/{record}/edit'),
        ];
    }
}
