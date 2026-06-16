<?php

namespace App\Filament\Resources\DoctorUnavailabilities;

use App\Filament\Resources\DoctorUnavailabilities\Pages\CreateDoctorUnavailability;
use App\Filament\Resources\DoctorUnavailabilities\Pages\EditDoctorUnavailability;
use App\Filament\Resources\DoctorUnavailabilities\Pages\ListDoctorUnavailabilities;
use App\Filament\Resources\DoctorUnavailabilities\Pages\ViewDoctorUnavailability;
use App\Filament\Resources\DoctorUnavailabilities\Schemas\DoctorUnavailabilityForm;
use App\Filament\Resources\DoctorUnavailabilities\Schemas\DoctorUnavailabilityInfolist;
use App\Filament\Resources\DoctorUnavailabilities\Tables\DoctorUnavailabilitiesTable;
use App\Models\DoctorUnavailability;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class DoctorUnavailabilityResource extends Resource
{
    protected static ?string $model = DoctorUnavailability::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNoSymbol;

    protected static ?string $modelLabel = 'Bloqueo de Agenda';

    protected static ?string $pluralModelLabel = 'Bloqueos de Agenda';

    protected static ?string $navigationLabel = 'Bloqueos de Agenda';

    protected static string|UnitEnum|null $navigationGroup = 'Agenda Médica';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return DoctorUnavailabilityForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DoctorUnavailabilityInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DoctorUnavailabilitiesTable::configure($table);
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
            'index' => ListDoctorUnavailabilities::route('/'),
            'create' => CreateDoctorUnavailability::route('/create'),
            'view' => ViewDoctorUnavailability::route('/{record}'),
            'edit' => EditDoctorUnavailability::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with('doctor.user');
    }
}
