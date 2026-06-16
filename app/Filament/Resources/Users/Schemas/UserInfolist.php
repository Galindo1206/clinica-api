<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Usuario')
                    ->icon(Heroicon::OutlinedUserCircle)
                    ->columns(2)
                    ->schema([
                        TextEntry::make('name')
                            ->label('Nombre'),

                        TextEntry::make('email')
                            ->label('Correo')
                            ->copyable(),

                        TextEntry::make('phone')
                            ->label('Teléfono')
                            ->placeholder('No registrado'),

                        TextEntry::make('role.name')
                            ->label('Rol')
                            ->formatStateUsing(fn (?string $state): string => self::roleLabels()[$state] ?? ucfirst((string) $state))
                            ->badge(),

                        TextEntry::make('is_active')
                            ->label('Estado')
                            ->formatStateUsing(fn (bool $state): string => $state ? 'Activo' : 'Inactivo')
                            ->badge()
                            ->color(fn (bool $state): string => $state ? 'success' : 'gray'),

                        TextEntry::make('created_at')
                            ->label('Registro')
                            ->dateTime('d/m/Y H:i'),
                    ]),
            ]);
    }

    private static function roleLabels(): array
    {
        return [
            'admin' => 'Admin',
            'doctor' => 'Médico',
            'receptionist' => 'Recepcionista',
            'patient' => 'Paciente',
        ];
    }
}
