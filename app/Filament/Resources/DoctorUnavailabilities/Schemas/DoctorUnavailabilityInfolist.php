<?php

namespace App\Filament\Resources\DoctorUnavailabilities\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class DoctorUnavailabilityInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Ficha de bloqueo de agenda')
                    ->description('Resumen del periodo de no disponibilidad médica.')
                    ->icon(Heroicon::OutlinedNoSymbol)
                    ->columns(3)
                    ->schema([
                        TextEntry::make('doctor.user.name')
                            ->label('Médico')
                            ->icon(Heroicon::OutlinedUserGroup)
                            ->weight('bold')
                            ->placeholder('No registrado'),

                        TextEntry::make('is_active')
                            ->label('Estado')
                            ->badge()
                            ->formatStateUsing(fn (bool $state): string => $state ? 'Activo' : 'Inactivo')
                            ->color(fn (bool $state): string => $state ? 'danger' : 'gray'),

                        TextEntry::make('created_at')
                            ->label('Creado')
                            ->icon(Heroicon::OutlinedCalendar)
                            ->dateTime('d/m/Y H:i'),

                        TextEntry::make('starts_at')
                            ->label('Inicio')
                            ->icon(Heroicon::OutlinedCalendarDays)
                            ->dateTime('d/m/Y H:i'),

                        TextEntry::make('ends_at')
                            ->label('Fin')
                            ->icon(Heroicon::OutlinedCalendarDays)
                            ->dateTime('d/m/Y H:i'),

                        TextEntry::make('reason')
                            ->label('Motivo')
                            ->icon(Heroicon::OutlinedClipboardDocument)
                            ->placeholder('No registrado')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
