<?php

namespace App\Filament\Resources\GunTypes;

use App\Filament\Resources\GunTypes\Pages\CreateGunType;
use App\Filament\Resources\GunTypes\Pages\EditGunType;
use App\Filament\Resources\GunTypes\Pages\ListGunTypes;
use App\Filament\Resources\GunTypes\Pages\ViewGunType;
use App\Filament\Resources\GunTypes\Schemas\GunTypeForm;
use App\Filament\Resources\GunTypes\Schemas\GunTypeInfolist;
use App\Filament\Resources\GunTypes\Tables\GunTypesTable;
use App\Models\GunType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GunTypeResource extends Resource
{
    protected static ?string $model = GunType::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $modelLabel = 'Typ broni';

    protected static ?string $pluralModelLabel = 'Typy broni';

    protected static ?string $navigationLabel = 'Typy broni';

    public static function form(Schema $schema): Schema
    {
        return GunTypeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return GunTypeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GunTypesTable::configure($table);
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
            'index' => ListGunTypes::route('/'),
            'create' => CreateGunType::route('/create'),
            'view' => ViewGunType::route('/{record}'),
            'edit' => EditGunType::route('/{record}/edit'),
        ];
    }
}
