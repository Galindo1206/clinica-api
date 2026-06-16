<?php

namespace App\Filament\Resources\AuditLogs\Tables;

use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AuditLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('user.name')
                    ->label('Usuario')
                    ->icon(Heroicon::OutlinedUser)
                    ->searchable()
                    ->sortable()
                    ->placeholder('Sistema / no registrado'),

                TextColumn::make('action')
                    ->label('Acción')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('module')
                    ->label('Módulo')
                    ->badge()
                    ->color('gray')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('patient.user.name')
                    ->label('Paciente')
                    ->icon(Heroicon::OutlinedUserCircle)
                    ->searchable()
                    ->placeholder('No aplica')
                    ->toggleable(),

                TextColumn::make('ip_address')
                    ->label('IP')
                    ->icon(Heroicon::OutlinedGlobeAlt)
                    ->searchable()
                    ->placeholder('No registrada')
                    ->toggleable(),

                TextColumn::make('user_agent')
                    ->label('User agent')
                    ->limit(45)
                    ->placeholder('No registrado')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->icon(Heroicon::OutlinedCalendarDays)
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Ver')
                    ->icon(Heroicon::OutlinedDocumentMagnifyingGlass),
            ])
            ->toolbarActions([]);
    }
}
