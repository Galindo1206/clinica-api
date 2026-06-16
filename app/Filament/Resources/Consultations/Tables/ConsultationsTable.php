<?php

namespace App\Filament\Resources\Consultations\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ConsultationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('consultation_date', 'desc')
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

                TextColumn::make('consultation_date')
                    ->label('Fecha de consulta')
                    ->icon(Heroicon::OutlinedCalendarDays)
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('reason')
                    ->label('Motivo')
                    ->limit(45)
                    ->searchable(),

                TextColumn::make('diagnosis')
                    ->label('Diagnóstico')
                    ->limit(45)
                    ->searchable()
                    ->placeholder('Pendiente'),

                TextColumn::make('created_at')
                    ->label('Creación')
                    ->icon(Heroicon::OutlinedCalendar)
                    ->dateTime('d/m/Y')
                    ->sortable()
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
}
