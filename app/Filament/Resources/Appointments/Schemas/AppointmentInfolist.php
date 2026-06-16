<?php

namespace App\Filament\Resources\Appointments\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class AppointmentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Ficha de cita')
                    ->description('Resumen administrativo de la cita médica.')
                    ->icon(Heroicon::OutlinedCalendarDays)
                    ->columns(3)
                    ->schema([
                        TextEntry::make('patient.user.name')
                            ->label('Paciente')
                            ->icon(Heroicon::OutlinedUser)
                            ->weight('bold')
                            ->placeholder('No registrado'),

                        TextEntry::make('doctor.user.name')
                            ->label('Médico')
                            ->icon(Heroicon::OutlinedUserGroup)
                            ->placeholder('No registrado'),

                        TextEntry::make('status')
                            ->label('Estado')
                            ->badge()
                            ->formatStateUsing(fn (?string $state): string => self::statusLabels()[$state] ?? 'Sin estado')
                            ->color(fn (?string $state): string => self::statusColors()[$state] ?? 'gray'),

                        TextEntry::make('scheduled_at')
                            ->label('Fecha y hora')
                            ->icon(Heroicon::OutlinedCalendarDays)
                            ->dateTime('d/m/Y H:i'),

                        TextEntry::make('duration_minutes')
                            ->label('Duración')
                            ->formatStateUsing(fn ($state): string => filled($state) ? "{$state} minutos" : 'No registrada')
                            ->icon(Heroicon::OutlinedClock),

                        TextEntry::make('created_at')
                            ->label('Creada')
                            ->icon(Heroicon::OutlinedCalendar)
                            ->dateTime('d/m/Y H:i'),

                        TextEntry::make('reason')
                            ->label('Motivo')
                            ->icon(Heroicon::OutlinedClipboardDocument)
                            ->columnSpanFull(),

                        TextEntry::make('notes')
                            ->label('Notas')
                            ->placeholder('No registradas')
                            ->columnSpanFull(),

                        TextEntry::make('status_reason')
                            ->label('Motivo de estado')
                            ->placeholder('No registrado')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    private static function statusLabels(): array
    {
        return [
            'pending' => 'Pendiente',
            'confirmed' => 'Confirmada',
            'cancelled' => 'Cancelada',
            'rejected' => 'Rechazada',
            'completed' => 'Completada',
            'no_show' => 'No asistió',
        ];
    }

    private static function statusColors(): array
    {
        return [
            'pending' => 'warning',
            'confirmed' => 'info',
            'cancelled' => 'danger',
            'rejected' => 'danger',
            'completed' => 'success',
            'no_show' => 'gray',
        ];
    }
}
