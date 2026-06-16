<?php

namespace App\Filament\Resources\Prescriptions\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PrescriptionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('issued_at', 'desc')
            ->columns([
                TextColumn::make('prescription_code')
                    ->label('Código')
                    ->badge()
                    ->copyable()
                    ->searchable(),

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

                TextColumn::make('consultation.id')
                    ->label('Consulta')
                    ->formatStateUsing(fn ($state): string => filled($state) ? "#{$state}" : 'No registrada')
                    ->icon(Heroicon::OutlinedClipboardDocumentList),

                TextColumn::make('issued_at')
                    ->label('Emisión')
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
                // TODO: Rehabilitar PDF cuando exista una ruta web segura compatible con la sesión de Filament.
            ])
            ->toolbarActions([]);
    }
}
