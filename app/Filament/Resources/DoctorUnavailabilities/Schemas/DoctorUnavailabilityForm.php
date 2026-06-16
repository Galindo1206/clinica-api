<?php

namespace App\Filament\Resources\DoctorUnavailabilities\Schemas;

use App\Models\Doctor;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class DoctorUnavailabilityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Médico')
                    ->description('Profesional cuya agenda quedará bloqueada.')
                    ->icon(Heroicon::OutlinedUserGroup)
                    ->schema([
                        Select::make('doctor_id')
                            ->label('Médico')
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

                Section::make('Rango de bloqueo')
                    ->description('Periodo en el que el médico no estará disponible.')
                    ->icon(Heroicon::OutlinedCalendarDays)
                    ->columns(2)
                    ->schema([
                        DateTimePicker::make('starts_at')
                            ->label('Fecha/hora inicio')
                            ->seconds(false)
                            ->required()
                            ->placeholder('Selecciona inicio'),

                        DateTimePicker::make('ends_at')
                            ->label('Fecha/hora fin')
                            ->seconds(false)
                            ->after('starts_at')
                            ->required()
                            ->placeholder('Selecciona fin'),
                    ]),

                Section::make('Motivo / descripción')
                    ->description('Razón administrativa del bloqueo.')
                    ->icon(Heroicon::OutlinedClipboardDocument)
                    ->schema([
                        Textarea::make('reason')
                            ->label('Motivo')
                            ->placeholder('Ej. Vacaciones, capacitación, ausencia, mantenimiento')
                            ->rows(3)
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ]),

                Section::make('Estado')
                    ->description('Permite activar o cancelar el bloqueo sin eliminarlo.')
                    ->icon(Heroicon::OutlinedShieldCheck)
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Bloqueo activo')
                            ->default(true),
                    ]),
            ]);
    }
}
