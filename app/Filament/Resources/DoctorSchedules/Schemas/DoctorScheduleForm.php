<?php

namespace App\Filament\Resources\DoctorSchedules\Schemas;

use App\Models\Doctor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class DoctorScheduleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Médico')
                    ->description('Profesional al que pertenece este horario de atención.')
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

                Section::make('Día y horario')
                    ->description('Bloque de atención usado por la disponibilidad automática.')
                    ->icon(Heroicon::OutlinedClock)
                    ->columns(2)
                    ->schema([
                        Select::make('day_of_week')
                            ->label('Día de semana')
                            ->options(self::dayOptions())
                            ->required()
                            ->placeholder('Selecciona un día'),

                        Select::make('slot_minutes')
                            ->label('Duración de cada turno')
                            ->options([
                                15 => '15 minutos',
                                20 => '20 minutos',
                                30 => '30 minutos',
                                45 => '45 minutos',
                                60 => '60 minutos',
                            ])
                            ->default(30)
                            ->required(),

                        TimePicker::make('start_time')
                            ->label('Hora inicio')
                            ->seconds(false)
                            ->required(),

                        TimePicker::make('end_time')
                            ->label('Hora fin')
                            ->seconds(false)
                            ->after('start_time')
                            ->required(),
                    ]),

                Section::make('Estado')
                    ->description('Permite activar o pausar este horario sin eliminarlo.')
                    ->icon(Heroicon::OutlinedShieldCheck)
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Horario activo')
                            ->default(true),
                    ]),
            ]);
    }

    private static function dayOptions(): array
    {
        return [
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
            0 => 'Domingo',
        ];
    }
}
