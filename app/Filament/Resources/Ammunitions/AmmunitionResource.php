<?php

namespace App\Filament\Resources\Ammunitions;

use App\Filament\Resources\Ammunitions\Pages\CreateAmmunition;
use App\Filament\Resources\Ammunitions\Pages\EditAmmunition;
use App\Filament\Resources\Ammunitions\Pages\ListAmmunitions;
use App\Filament\Resources\Ammunitions\Pages\ViewAmmunition;
use App\Filament\Resources\Ammunitions\Schemas\AmmunitionForm;
use App\Filament\Resources\Ammunitions\Schemas\AmmunitionInfolist;
use App\Filament\Resources\Ammunitions\Tables\AmmunitionsTable;
use App\Models\Ammunition;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AmmunitionResource extends Resource
{
    protected static ?string $model = Ammunition::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $modelLabel = 'Amunicja';

    protected static ?string $pluralModelLabel = 'Amunicja';

    protected static ?string $navigationLabel = 'Amunicja';

    public static function form(Schema $schema): Schema
    {
        return AmmunitionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AmmunitionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AmmunitionsTable::configure($table);
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
            'index' => ListAmmunitions::route('/'),
            'create' => CreateAmmunition::route('/create'),
            'view' => ViewAmmunition::route('/{record}'),
            'edit' => EditAmmunition::route('/{record}/edit'),
        ];
    }
}
