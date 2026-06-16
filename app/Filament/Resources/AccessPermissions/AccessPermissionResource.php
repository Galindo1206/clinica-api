<?php

namespace App\Filament\Resources\AccessPermissions;

use App\Filament\Resources\AccessPermissions\Pages\CreateAccessPermission;
use App\Filament\Resources\AccessPermissions\Pages\EditAccessPermission;
use App\Filament\Resources\AccessPermissions\Pages\ListAccessPermissions;
use App\Filament\Resources\AccessPermissions\Pages\ViewAccessPermission;
use App\Filament\Resources\AccessPermissions\Schemas\AccessPermissionForm;
use App\Filament\Resources\AccessPermissions\Schemas\AccessPermissionInfolist;
use App\Filament\Resources\AccessPermissions\Tables\AccessPermissionsTable;
use App\Models\AccessPermission;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class AccessPermissionResource extends Resource
{
    protected static ?string $model = AccessPermission::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedKey;

    protected static ?string $modelLabel = 'Permiso de Acceso';

    protected static ?string $pluralModelLabel = 'Permisos de Acceso';

    protected static ?string $navigationLabel = 'Permisos de Acceso';

    protected static string|UnitEnum|null $navigationGroup = 'Seguridad';

    public static function form(Schema $schema): Schema
    {
        return AccessPermissionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AccessPermissionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AccessPermissionsTable::configure($table);
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
            'index' => ListAccessPermissions::route('/'),
            'create' => CreateAccessPermission::route('/create'),
            'view' => ViewAccessPermission::route('/{record}'),
            'edit' => EditAccessPermission::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['patient.user', 'doctor.user', 'grantedBy']);
    }
}
