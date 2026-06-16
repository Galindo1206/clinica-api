<?php

namespace App\Filament\Resources\DoctorSchedules\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DoctorSchedulesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('day_of_week')
            ->columns([
                TextColumn::make('doctor.user.name')
                    ->label('Médico')
                    ->icon(Heroicon::OutlinedUserGroup)
                    ->searchable()
                    ->sortable()
                    ->placeholder('No registrado'),

                TextColumn::make('day_of_week')
                    ->label('Día')
                    ->badge()
                    ->formatStateUsing(fn ($state): string => self::dayLabels()[(int) $state] ?? 'No registrado')
                    ->color('info')
                    ->sortable(),

                TextColumn::make('start_time')
                    ->label('Hora inicio')
                    ->icon(Heroicon::OutlinedClock)
                    ->sortable(),

                TextColumn::make('end_time')
                    ->label('Hora fin')
                    ->icon(Heroicon::OutlinedClock)
                    ->sortable(),

                TextColumn::make('slot_minutes')
                    ->label('Turno')
                    ->formatStateUsing(fn ($state): string => filled($state) ? "{$state} min" : 'No registrado')
                    ->sortable(),

                TextColumn::make('is_active')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Activo' : 'Inactivo')
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger')
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
