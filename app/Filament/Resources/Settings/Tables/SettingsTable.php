<?php

namespace App\Filament\Resources\Settings\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('updated_at', 'desc')
            ->columns([
                TextColumn::make('business_name')
                    ->label('Nombre de clínica')
                    ->icon(Heroicon::OutlinedBuildingOffice)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('ruc')
                    ->label('RUC')
                    ->searchable()
                    ->placeholder('No registrado'),

                TextColumn::make('phone')
                    ->label('Teléfono')
                    ->icon(Heroicon::OutlinedPhone)
                    ->searchable()
                    ->placeholder('No registrado'),

                TextColumn::make('email')
                    ->label('Email')
                    ->icon(Heroicon::OutlinedEnvelope)
                    ->searchable()
                    ->placeholder('No registrado'),

                TextColumn::make('address')
                    ->label('Dirección')
                    ->limit(40)
                    ->placeholder('No registrada')
                    ->toggleable(),

                TextColumn::make('updated_at')
                    ->label('Actualizado')
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
