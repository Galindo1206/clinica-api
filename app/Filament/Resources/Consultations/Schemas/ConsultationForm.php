<?php

namespace App\Filament\Resources\Consultations\Schemas;

use App\Models\Doctor;
use App\Models\Patient;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ConsultationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información general')
                    ->description('Paciente, médico responsable y fecha de atención.')
                    ->icon(Heroicon::OutlinedClipboardDocumentList)
                    ->columns(2)
                    ->schema([
                        Select::make('patient_id')
                            ->label('Paciente')
                            ->options(fn () => Patient::with('user')
                                ->get()
                                ->sortBy(fn (Patient $patient): string => $patient->user?->name ?? '')
                                ->mapWithKeys(fn (Patient $patient): array => [
                                    $patient->id => $patient->user?->name ?? "Paciente #{$patient->id}",
                                ]))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->placeholder('Selecciona un paciente'),

                        Select::make('doctor_id')
                            ->label('Médico')
                            ->options(fn () => Doctor::with('user')
                                ->get()
                                ->sortBy(fn (Doctor $doctor): string => $doctor->user?->name ?? '')
                                ->mapWithKeys(fn (Doctor $doctor): array => [
                                    $doctor->id => $doctor->user?->name ?? "Médico #{$doctor->id}",
                                ]))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->placeholder('Selecciona un médico'),

                        DateTimePicker::make('consultation_date')
                            ->label('Fecha de consulta')
                            ->required()
                            ->seconds(false)
                            ->placeholder('Selecciona fecha y hora'),

                        TextInput::make('reason')
                            ->label('Motivo de consulta')
                            ->required()
                            ->placeholder('Ej. Dolor abdominal, control médico')
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ]),

                Section::make('Datos clínicos')
                    ->description('Información clínica registrada durante la atención.')
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->columns(2)
                    ->schema([
                        Textarea::make('symptoms')
                            ->label('Síntomas')
                            ->placeholder('Describe los síntomas referidos por el paciente')
                            ->rows(4)
                            ->columnSpanFull(),

                        Textarea::make('diagnosis')
                            ->label('Diagnóstico')
                            ->placeholder('Diagnóstico clínico, si corresponde')
                            ->rows(4),

                        Textarea::make('treatment')
                            ->label('Tratamiento')
                            ->placeholder('Indicaciones o tratamiento definido')
                            ->rows(4),
                    ]),

                Section::make('Observaciones')
                    ->description('Notas complementarias para la ficha clínica.')
                    ->icon(Heroicon::OutlinedClipboardDocument)
                    ->schema([
                        Textarea::make('observations')
                            ->label('Notas u observaciones')
                            ->placeholder('Observaciones adicionales de la consulta')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
