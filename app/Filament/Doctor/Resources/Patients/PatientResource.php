<?php

namespace App\Filament\Doctor\Resources\Patients;

use App\Filament\Doctor\Resources\Patients\Pages\ListPatients;
use App\Filament\Doctor\Resources\Patients\Pages\ViewPatient;
use App\Models\Patient;
use BackedEnum;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PatientResource extends Resource
{
    protected static ?string $model = Patient::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $navigationLabel = 'Pacientes';

    protected static ?string $modelLabel = 'Paciente';

    protected static ?string $pluralModelLabel = 'Pacientes';

    protected static ?int $navigationSort = 2;

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Ficha básica')
                ->columns(2)
                ->schema([
                    TextEntry::make('user.name')->label('Paciente'),
                    TextEntry::make('user.email')->label('Correo')->placeholder('No registrado'),
                    TextEntry::make('user.phone')->label('Teléfono')->placeholder('No registrado'),
                    TextEntry::make('user.document_number')->label('Documento')->placeholder('No registrado'),
                    TextEntry::make('blood_type')->label('Tipo de sangre')->placeholder('No registrado'),
                    TextEntry::make('insurance_name')->label('Seguro')->placeholder('No registrado'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('user.name')->label('Paciente')->searchable()->sortable(),
                TextColumn::make('user.document_number')->label('Documento')->searchable()->placeholder('No registrado'),
                TextColumn::make('user.phone')->label('Teléfono')->placeholder('No registrado'),
                TextColumn::make('blood_type')->label('Sangre')->badge()->placeholder('No registrado'),
            ])
            ->recordActions([
                ViewAction::make()->label('Ver'),
            ])
            ->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPatients::route('/'),
            'view' => ViewPatient::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with('user');
        $doctorId = auth()->user()?->doctor?->id;

        if (! $doctorId) {
            return $query;
        }

        return $query->where(function (Builder $query) use ($doctorId): void {
            $query->whereHas('consultations', fn (Builder $consultations) => $consultations->where('doctor_id', $doctorId))
                ->orWhereHas('accessPermissions', function (Builder $permissions) use ($doctorId): void {
                    $permissions->where('doctor_id', $doctorId)
                        ->where('is_active', true)
                        ->where(fn (Builder $q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
                        ->where(fn (Builder $q) => $q->whereNull('expires_at')->orWhere('expires_at', '>=', now()));
                });
        });
    }
}
