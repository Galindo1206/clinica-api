<?php

namespace App\Filament\Resources\MedicalDocuments\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MedicalDocumentsTable
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

                TextColumn::make('title')
                    ->label('Documento')
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('document_type')
                    ->label('Tipo')
                    ->badge()
                    ->color('info')
                    ->searchable(),

                TextColumn::make('document_date')
                    ->label('Fecha del documento')
                    ->icon(Heroicon::OutlinedCalendar)
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('No registrada'),

                TextColumn::make('uploadedBy.name')
                    ->label('Subido por')
                    ->icon(Heroicon::OutlinedUserCircle)
                    ->searchable()
                    ->placeholder('No registrado'),

                TextColumn::make('created_at')
                    ->label('Fecha de subida')
                    ->icon(Heroicon::OutlinedCalendarDays)
                    ->dateTime('d/m/Y H:i')
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
}
