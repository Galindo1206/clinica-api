<?php

namespace App\Filament\Reception\Resources\Patients;

use App\Filament\Reception\Resources\Patients\Pages\CreatePatient;
use App\Filament\Reception\Resources\Patients\Pages\EditPatient;
use App\Filament\Reception\Resources\Patients\Pages\ListPatients;
use App\Filament\Reception\Resources\Patients\Pages\ViewPatient;
use App\Models\Patient;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;

class PatientResource extends Resource
{
    protected static ?string $model = Patient::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $modelLabel = 'Paciente';

    protected static ?string $pluralModelLabel = 'Pacientes';

    protected static ?string $navigationLabel = 'Pacientes';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Datos básicos')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre completo')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('Correo')
                            ->email()
                            ->required()
                            ->rule(fn ($record) => Rule::unique('users', 'email')->ignore($record?->user?->id))
                            ->maxLength(255),

                        TextInput::make('phone')
                            ->label('Teléfono')
                            ->tel()
                            ->maxLength(20),

                        Select::make('document_type')
                            ->label('Tipo de documento')
                            ->options([
                                'DNI' => 'DNI',
                                'CE' => 'Carné de extranjería',
                                'PASSPORT' => 'Pasaporte',
                            ]),

                        TextInput::make('document_number')
                            ->label('Número de documento')
                            ->maxLength(30),

                        DatePicker::make('birth_date')
                            ->label('Fecha de nacimiento'),
                    ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Ficha básica')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('user.name')->label('Paciente'),
                        TextEntry::make('user.email')->label('Correo')->copyable(),
                        TextEntry::make('user.phone')->label('Teléfono')->placeholder('No registrado'),
                        TextEntry::make('user.document_number')->label('Documento')->placeholder('No registrado'),
                        TextEntry::make('created_at')->label('Registro')->dateTime('d/m/Y H:i'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('user.name')
                    ->label('Paciente')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.document_number')
                    ->label('Documento')
                    ->searchable()
                    ->placeholder('No registrado'),

                TextColumn::make('user.phone')
                    ->label('Teléfono')
                    ->searchable()
                    ->placeholder('No registrado'),

                TextColumn::make('user.email')
                    ->label('Correo')
                    ->searchable()
                    ->placeholder('No registrado'),

                TextColumn::make('created_at')
                    ->label('Registro')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->recordActions([
                ViewAction::make()->label('Ver'),
                EditAction::make()->label('Editar'),
            ])
            ->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPatients::route('/'),
            'create' => CreatePatient::route('/create'),
            'view' => ViewPatient::route('/{record}'),
            'edit' => EditPatient::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('user');
    }
}
