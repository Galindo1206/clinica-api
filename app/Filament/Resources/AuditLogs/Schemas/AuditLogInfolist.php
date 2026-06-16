<?php

namespace App\Filament\Resources\AuditLogs\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class AuditLogInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Ficha del evento de auditoría')
                    ->description('Registro read-only generado por el sistema.')
                    ->icon(Heroicon::OutlinedShieldCheck)
                    ->columns(3)
                    ->schema([
                        TextEntry::make('action')
                            ->label('Acción')
                            ->badge()
                            ->color('info')
                            ->copyable(),

                        TextEntry::make('module')
                            ->label('Módulo')
                            ->badge()
                            ->color('gray')
                            ->placeholder('No registrado'),

                        TextEntry::make('created_at')
                            ->label('Fecha')
                            ->icon(Heroicon::OutlinedCalendarDays)
                            ->dateTime('d/m/Y H:i:s'),

                        TextEntry::make('user.name')
                            ->label('Usuario')
                            ->icon(Heroicon::OutlinedUser)
                            ->placeholder('Sistema / no registrado'),

                        TextEntry::make('patient.user.name')
                            ->label('Paciente relacionado')
                            ->icon(Heroicon::OutlinedUserCircle)
                            ->placeholder('No aplica'),

                        TextEntry::make('ip_address')
                            ->label('IP')
                            ->icon(Heroicon::OutlinedGlobeAlt)
                            ->copyable()
                            ->placeholder('No registrada'),

                        TextEntry::make('description')
                            ->label('Descripción')
                            ->placeholder('No registrada')
                            ->columnSpanFull(),

                        TextEntry::make('user_agent')
                            ->label('User agent')
                            ->placeholder('No registrado')
                            ->copyable()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
