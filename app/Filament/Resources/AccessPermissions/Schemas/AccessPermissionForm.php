<?php

namespace App\Filament\Resources\AccessPermissions\Schemas;

use App\Models\Doctor;
use App\Models\Patient;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class AccessPermissionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Paciente')
                    ->description('Expediente médico al que se concederá acceso.')
                    ->icon(Heroicon::OutlinedUser)
                    ->schema([
                        Select::make('patient_id')
                            ->label('Paciente')
                            ->options(fn () => Patient::with('user')
                                ->get()
                                ->sortBy(fn (Patient $patient): string => $patient->user?->name ?? '')
                                ->mapWithKeys(fn (Patient $patient): array => [
                                    $patient->id => $patient->user?->name ?? "Paciente #{$patient->id}",
                                ]))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->placeholder('Selecciona un paciente'),
                    ]),

                Section::make('Usuario/Médico autorizado')
                    ->description('Médico que podrá acceder al expediente médico del paciente.')
                    ->icon(Heroicon::OutlinedUserGroup)
                    ->schema([
                        Select::make('doctor_id')
                            ->label('Médico autorizado')
                            ->options(fn () => Doctor::with('user')
                                ->get()
                                ->sortBy(fn (Doctor $doctor): string => $doctor->user?->name ?? '')
                                ->mapWithKeys(fn (Doctor $doctor): array => [
                                    $doctor->id => $doctor->user?->name ?? "Médico #{$doctor->id}",
                                ]))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->placeholder('Selecciona un médico'),
                    ]),

                Section::make('Alcance del permiso')
                    ->description('Nivel de acceso concedido sobre la bóveda médica.')
                    ->icon(Heroicon::OutlinedShieldCheck)
                    ->schema([
                        Select::make('permission_type')
                            ->label('Tipo de permiso')
                            ->options([
                                'read_only' => 'Solo lectura',
                                'read_write' => 'Lectura y escritura',
                                'emergency' => 'Emergencia',
                            ])
                            ->default('read_only')
                            ->required(),
                    ]),

                Section::make('Vigencia')
                    ->description('Fechas opcionales que limitan cuándo aplica el permiso.')
                    ->icon(Heroicon::OutlinedCalendarDays)
                    ->columns(2)
                    ->schema([
                        DateTimePicker::make('starts_at')
                            ->label('Inicio')
                            ->seconds(false)
                            ->placeholder('Sin fecha de inicio'),

                        DateTimePicker::make('expires_at')
                            ->label('Fin')
                            ->seconds(false)
                            ->afterOrEqual('starts_at')
                            ->placeholder('Sin fecha de fin'),
                    ]),

                Section::make('Estado')
                    ->description('Permite activar o revocar visualmente este permiso.')
                    ->icon(Heroicon::OutlinedKey)
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Permiso activo')
                            ->default(true),
                    ]),
            ]);
    }
}
