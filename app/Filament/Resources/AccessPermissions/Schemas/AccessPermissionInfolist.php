<?php

namespace App\Filament\Resources\AccessPermissions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class AccessPermissionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Ficha del permiso de acceso')
                    ->description('Resumen del acceso concedido a expediente médico.')
                    ->icon(Heroicon::OutlinedKey)
                    ->columns(3)
                    ->schema([
                        TextEntry::make('patient.user.name')
                            ->label('Paciente')
                            ->icon(Heroicon::OutlinedUser)
                            ->weight('bold')
                            ->placeholder('No registrado'),

                        TextEntry::make('doctor.user.name')
                            ->label('Médico autorizado')
                            ->icon(Heroicon::OutlinedUserGroup)
                            ->placeholder('No registrado'),

                        TextEntry::make('is_active')
                            ->label('Estado')
                            ->badge()
                            ->formatStateUsing(fn (bool $state): string => $state ? 'Activo' : 'Inactivo')
                            ->color(fn (bool $state): string => $state ? 'success' : 'danger'),

                        TextEntry::make('permission_type')
                            ->label('Tipo de permiso')
                            ->badge()
                            ->formatStateUsing(fn (?string $state): string => self::permissionLabels()[$state] ?? 'No registrado')
                            ->color(fn (?string $state): string => self::permissionColors()[$state] ?? 'gray'),

                        TextEntry::make('starts_at')
                            ->label('Inicio')
                            ->icon(Heroicon::OutlinedCalendarDays)
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('Sin inicio definido'),

                        TextEntry::make('expires_at')
                            ->label('Fin')
                            ->icon(Heroicon::OutlinedCalendarDays)
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('Sin fin definido'),

                        TextEntry::make('grantedBy.name')
                            ->label('Otorgado por')
                            ->icon(Heroicon::OutlinedUserCircle)
                            ->placeholder('No registrado'),

                        TextEntry::make('created_at')
                            ->label('Creado')
                            ->icon(Heroicon::OutlinedCalendar)
                            ->dateTime('d/m/Y H:i'),
                    ]),
            ]);
    }

    private static function permissionLabels(): array
    {
        return [
            'read_only' => 'Solo lectura',
            'read_write' => 'Lectura y escritura',
            'emergency' => 'Emergencia',
        ];
    }

    private static function permissionColors(): array
    {
        return [
            'read_only' => 'info',
            'read_write' => 'warning',
            'emergency' => 'danger',
        ];
    }
}
