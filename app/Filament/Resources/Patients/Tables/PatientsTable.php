<?php

namespace App\Filament\Resources\Patients\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PatientsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('user.name')
                    ->label('Paciente')
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

                TextColumn::make('blood_type')
                    ->label('Sangre')
                    ->badge()
                    ->color('danger')
                    ->placeholder('No registrado'),

                TextColumn::make('insurance_name')
                    ->label('Seguro')
                    ->icon(Heroicon::OutlinedShieldCheck)
                    ->searchable()
                    ->placeholder('Sin seguro'),

                TextColumn::make('emergency_contact_phone')
                    ->label('Emergencia')
                    ->icon(Heroicon::OutlinedPhoneArrowUpRight)
                    ->placeholder('No registrado')
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Registro')
                    ->icon(Heroicon::OutlinedCalendarDays)
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('blood_type')
                    ->label('Tipo de sangre')
                    ->options([
                        'A+' => 'A+',
                        'A-' => 'A-',
                        'B+' => 'B+',
                        'B-' => 'B-',
                        'AB+' => 'AB+',
                        'AB-' => 'AB-',
                        'O+' => 'O+',
                        'O-' => 'O-',
                    ]),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Ver')
                    ->icon(Heroicon::OutlinedDocumentMagnifyingGlass),
                EditAction::make()
                    ->label('Editar')
                    ->icon(Heroicon::OutlinedPencilSquare),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Eliminar seleccionados'),
                ]),
            ]);
    }
}
