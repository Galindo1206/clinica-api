<?php

namespace App\Filament\Resources\Appointments\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AppointmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('scheduled_at', 'desc')
            ->columns([
                TextColumn::make('patient.user.name')
                    ->label('Paciente')
                    ->icon(Heroicon::OutlinedUser)
                    ->searchable()
                    ->sortable()
                    ->placeholder('No registrado'),

                TextColumn::make('doctor.user.name')
                    ->label('Médico')
                    ->icon(Heroicon::OutlinedUserGroup)
                    ->searchable()
                    ->sortable()
                    ->placeholder('No registrado'),

                TextColumn::make('scheduled_at')
                    ->label('Fecha/hora')
                    ->icon(Heroicon::OutlinedCalendarDays)
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('duration_minutes')
                    ->label('Duración')
                    ->formatStateUsing(fn ($state): string => filled($state) ? "{$state} min" : 'No registrada')
                    ->sortable(),

                TextColumn::make('reason')
                    ->label('Motivo')
                    ->limit(40)
                    ->searchable(),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => self::statusLabels()[$state] ?? 'Sin estado')
                    ->color(fn (?string $state): string => self::statusColors()[$state] ?? 'gray')
                    ->sortable(),

                TextColumn::make('status_reason')
                    ->label('Motivo de estado')
                    ->limit(35)
                    ->placeholder('No registrado')
                    ->toggleable(),
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
