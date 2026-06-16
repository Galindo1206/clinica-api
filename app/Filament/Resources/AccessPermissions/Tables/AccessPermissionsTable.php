<?php

namespace App\Filament\Resources\AccessPermissions\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AccessPermissionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('patient.user.name')
                    ->label('Paciente')
                    ->icon(Heroicon::OutlinedUser)
                    ->searchable()
                    ->sortable()
                    ->placeholder('No registrado'),

                TextColumn::make('doctor.user.name')
                    ->label('Médico autorizado')
                    ->icon(Heroicon::OutlinedUserGroup)
                    ->searchable()
                    ->sortable()
                    ->placeholder('No registrado'),

                TextColumn::make('permission_type')
                    ->label('Tipo de permiso')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => self::permissionLabels()[$state] ?? 'No registrado')
                    ->color(fn (?string $state): string => self::permissionColors()[$state] ?? 'gray')
                    ->sortable(),

                TextColumn::make('starts_at')
                    ->label('Inicio')
                    ->icon(Heroicon::OutlinedCalendarDays)
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Sin inicio')
                    ->sortable(),

                TextColumn::make('expires_at')
                    ->label('Fin')
                    ->icon(Heroicon::OutlinedCalendarDays)
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Sin fin')
                    ->sortable(),

                TextColumn::make('is_active')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Activo' : 'Inactivo')
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->icon(Heroicon::OutlinedCalendar)
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Ver')
                    ->icon(Heroicon::OutlinedDocumentMagnifyingGlass),
                EditAction::make()
                    ->label('Editar')
                    ->icon(Heroicon::OutlinedPencilSquare),
            ])
            ->toolbarActions([]);
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
