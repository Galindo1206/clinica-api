<?php

namespace App\Filament\Resources\Consultations\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ConsultationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Ficha clínica de consulta')
                    ->description('Resumen general de la atención registrada.')
                    ->icon(Heroicon::OutlinedClipboardDocumentList)
                    ->columns(3)
                    ->schema([
                        TextEntry::make('patient.user.name')
                            ->label('Paciente')
                            ->icon(Heroicon::OutlinedUser)
                            ->weight('bold')
                            ->placeholder('No registrado'),

                        TextEntry::make('doctor.user.name')
                            ->label('Médico')
                            ->icon(Heroicon::OutlinedUserGroup)
                            ->placeholder('No registrado'),

                        TextEntry::make('consultation_date')
                            ->label('Fecha')
                            ->icon(Heroicon::OutlinedCalendarDays)
                            ->dateTime('d/m/Y H:i'),

                        TextEntry::make('reason')
                            ->label('Motivo')
                            ->icon(Heroicon::OutlinedClipboardDocument)
                            ->columnSpanFull(),
                    ]),

                Section::make('Datos clínicos')
                    ->description('Síntomas, diagnóstico y tratamiento documentados.')
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->columns(2)
                    ->schema([
                        TextEntry::make('symptoms')
                            ->label('Síntomas')
                            ->placeholder('No registrado')
                            ->columnSpanFull(),

                        TextEntry::make('diagnosis')
                            ->label('Diagnóstico')
                            ->placeholder('No registrado'),

                        TextEntry::make('treatment')
                            ->label('Tratamiento')
                            ->placeholder('No registrado'),

                        TextEntry::make('observations')
                            ->label('Notas')
                            ->placeholder('No registrado')
                            ->columnSpanFull(),
                    ]),

                Section::make('Signos vitales')
                    ->description('Datos vitales asociados a la consulta, si fueron registrados.')
                    ->icon(Heroicon::OutlinedHeart)
                    ->columns(4)
                    ->visible(fn ($record): bool => filled($record->vitals))
                    ->schema([
                        TextEntry::make('vitals.weight')
                            ->label('Peso')
                            ->placeholder('No registrado'),

                        TextEntry::make('vitals.height')
                            ->label('Talla')
                            ->placeholder('No registrado'),

                        TextEntry::make('vitals.temperature')
                            ->label('Temperatura')
                            ->placeholder('No registrado'),

                        TextEntry::make('vitals.blood_pressure')
                            ->label('Presión arterial')
                            ->placeholder('No registrado'),

                        TextEntry::make('vitals.heart_rate')
                            ->label('Frecuencia cardiaca')
                            ->placeholder('No registrado'),

                        TextEntry::make('vitals.respiratory_rate')
                            ->label('Frecuencia respiratoria')
                            ->placeholder('No registrado'),

                        TextEntry::make('vitals.oxygen_saturation')
                            ->label('Saturación O2')
                            ->placeholder('No registrado'),
                    ]),
            ]);
    }
}
