<?php

namespace App\Filament\Resources\Doctors\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DoctorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('user.name')
                    ->label('Médico')
                    ->description(fn ($record): ?string => $record->user?->document_number
                        ? ($record->user?->document_type . ': ' . $record->user?->document_number)
                        : null)
                    ->icon(Heroicon::OutlinedUserCircle)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.email')
                    ->label('Correo')
                    ->icon(Heroicon::OutlinedEnvelope)
                    ->searchable()
                    ->copyable()
                    ->placeholder('Sin correo'),

                TextColumn::make('user.phone')
                    ->label('Teléfono')
                    ->icon(Heroicon::OutlinedPhone)
                    ->searchable()
                    ->placeholder('No registrado'),

                TextColumn::make('specialty')
                    ->label('Especialidad')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('cmp_number')
                    ->label('CMP')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('license_number')
                    ->label('RNE')
                    ->searchable()
                    ->copyable()
                    ->placeholder('No registrado')
                    ->toggleable(),

                TextColumn::make('user.is_active')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Activo' : 'Inactivo')
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Registro')
                    ->icon(Heroicon::OutlinedCalendarDays)
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
}
