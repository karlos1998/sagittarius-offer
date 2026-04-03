<?php

namespace App\Filament\Resources\GunPackages;

use App\Filament\Resources\GunPackages\Pages\CreateGunPackage;
use App\Filament\Resources\GunPackages\Pages\EditGunPackage;
use App\Filament\Resources\GunPackages\Pages\ListGunPackages;
use App\Filament\Resources\GunPackages\Pages\ViewGunPackage;
use App\Filament\Resources\GunPackages\Schemas\GunPackageForm;
use App\Filament\Resources\GunPackages\Schemas\GunPackageInfolist;
use App\Filament\Resources\GunPackages\Tables\GunPackagesTable;
use App\Models\GunPackage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GunPackageResource extends Resource
{
    protected static ?string $model = GunPackage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArchiveBox;

    protected static ?string $modelLabel = 'Pakiet';

    protected static ?string $pluralModelLabel = 'Pakiety';

    protected static ?string $navigationLabel = 'Pakiety';

    public static function form(Schema $schema): Schema
    {
        return GunPackageForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return GunPackageInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GunPackagesTable::configure($table);
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
            'index' => ListGunPackages::route('/'),
            'create' => CreateGunPackage::route('/create'),
            'view' => ViewGunPackage::route('/{record}'),
            'edit' => EditGunPackage::route('/{record}/edit'),
        ];
    }
}
