<?php

namespace App\Filament\Resources\DoctorSchedules\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class DoctorScheduleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Ficha de horario médico')
                    ->description('Bloque de atención configurado para disponibilidad.')
                    ->icon(Heroicon::OutlinedClock)
                    ->columns(3)
                    ->schema([
                        TextEntry::make('doctor.user.name')
                            ->label('Médico')
                            ->icon(Heroicon::OutlinedUserGroup)
                            ->weight('bold')
                            ->placeholder('No registrado'),

                        TextEntry::make('day_of_week')
                            ->label('Día')
                            ->badge()
                            ->formatStateUsing(fn ($state): string => self::dayLabels()[(int) $state] ?? 'No registrado')
                            ->color('info'),

                        TextEntry::make('is_active')
                            ->label('Estado')
                            ->badge()
                            ->formatStateUsing(fn (bool $state): string => $state ? 'Activo' : 'Inactivo')
                            ->color(fn (bool $state): string => $state ? 'success' : 'danger'),

                        TextEntry::make('start_time')
                            ->label('Hora inicio')
                            ->icon(Heroicon::OutlinedClock),

                        TextEntry::make('end_time')
                            ->label('Hora fin')
                            ->icon(Heroicon::OutlinedClock),

                        TextEntry::make('slot_minutes')
                            ->label('Duración de turno')
                            ->formatStateUsing(fn ($state): string => filled($state) ? "{$state} minutos" : 'No registrada'),

                        TextEntry::make('created_at')
                            ->label('Creado')
                            ->icon(Heroicon::OutlinedCalendarDays)
                            ->dateTime('d/m/Y H:i'),
                    ]),
            ]);
    }

    private static function dayLabels(): array
    {
        return [
            0 => 'Domingo',
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
        ];
    }
}
