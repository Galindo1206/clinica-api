<?php

namespace App\Filament\Resources\Prescriptions\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class PrescriptionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Ficha de receta')
                    ->description('Resumen de la receta electrónica emitida.')
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->columns(3)
                    ->schema([
                        TextEntry::make('prescription_code')
                            ->label('Código')
                            ->icon(Heroicon::OutlinedHashtag)
                            ->badge()
                            ->copyable(),

                        TextEntry::make('issued_at')
                            ->label('Fecha')
                            ->icon(Heroicon::OutlinedCalendarDays)
                            ->dateTime('d/m/Y H:i'),

                        TextEntry::make('consultation.id')
                            ->label('Consulta')
                            ->formatStateUsing(fn ($state): string => filled($state) ? "Consulta #{$state}" : 'No registrada')
                            ->icon(Heroicon::OutlinedClipboardDocumentList),

                        TextEntry::make('patient.user.name')
                            ->label('Paciente')
                            ->icon(Heroicon::OutlinedUser)
                            ->placeholder('No registrado'),

                        TextEntry::make('doctor.user.name')
                            ->label('Médico')
                            ->icon(Heroicon::OutlinedUserGroup)
                            ->placeholder('No registrado'),

                        TextEntry::make('consultation.diagnosis')
                            ->label('Diagnóstico / consulta')
                            ->placeholder('No registrado')
                            ->columnSpanFull(),
                    ]),

                Section::make('Indicaciones')
                    ->description('Indicaciones generales entregadas al paciente.')
                    ->icon(Heroicon::OutlinedClipboardDocument)
                    ->schema([
                        TextEntry::make('general_indications')
                            ->label('Indicaciones generales')
                            ->placeholder('No registrado')
                            ->columnSpanFull(),
                    ]),

                Section::make('Medicamentos')
                    ->description('Detalle de medicamentos indicados en la receta.')
                    ->icon(Heroicon::OutlinedBeaker)
                    ->schema([
                        RepeatableEntry::make('items')
                            ->label('Medicamentos')
                            ->schema([
                                TextEntry::make('medicine_name')
                                    ->label('Medicamento')
                                    ->weight('bold'),

                                TextEntry::make('dosage')
                                    ->label('Dosis')
                                    ->placeholder('No registrada'),

                                TextEntry::make('frequency')
                                    ->label('Frecuencia')
                                    ->placeholder('No registrada'),

                                TextEntry::make('duration')
                                    ->label('Duración')
                                    ->placeholder('No registrada'),

                                TextEntry::make('instructions')
                                    ->label('Instrucciones')
                                    ->placeholder('No registradas')
                                    ->columnSpanFull(),
                            ])
                            ->columns(4)
                            ->placeholder('No hay medicamentos registrados'),
                    ]),
            ]);
    }
}
