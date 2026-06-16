<?php

namespace App\Filament\Resources\Prescriptions\Schemas;

use App\Models\Consultation;
use App\Models\Doctor;
use App\Models\Patient;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Validation\Rule;

class PrescriptionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información de receta')
                    ->description('Datos principales de la receta electrónica y su consulta relacionada.')
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->columns(2)
                    ->schema([
                        TextInput::make('prescription_code')
                            ->label('Código de receta')
                            ->placeholder('Ej. RX-000001')
                            ->required()
                            ->rule(function ($record) {
                                return Rule::unique('prescriptions', 'prescription_code')
                                    ->ignore($record?->id);
                            })
                            ->maxLength(255),

                        DateTimePicker::make('issued_at')
                            ->label('Fecha de emisión')
                            ->required()
                            ->seconds(false)
                            ->placeholder('Selecciona fecha y hora'),

                        Select::make('consultation_id')
                            ->label('Consulta relacionada')
                            ->options(fn () => Consultation::with(['patient.user', 'doctor.user'])
                                ->latest('consultation_date')
                                ->get()
                                ->mapWithKeys(fn (Consultation $consultation): array => [
                                    $consultation->id => sprintf(
                                        '#%s - %s con %s (%s)',
                                        $consultation->id,
                                        $consultation->patient?->user?->name ?? 'Paciente no registrado',
                                        $consultation->doctor?->user?->name ?? 'Médico no registrado',
                                        $consultation->consultation_date?->format('d/m/Y H:i') ?? 'sin fecha',
                                    ),
                                ]))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->placeholder('Selecciona una consulta')
                            ->columnSpanFull(),

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
                    ]),

                Section::make('Indicaciones generales')
                    ->description('Instrucciones generales para el paciente.')
                    ->icon(Heroicon::OutlinedClipboardDocument)
                    ->schema([
                        Textarea::make('general_indications')
                            ->label('Indicaciones')
                            ->placeholder('Ej. Tomar después de alimentos, evitar alcohol, regresar a control')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),

                Section::make('Medicamentos')
                    ->description('Detalle de medicamentos, dosis y duración indicados.')
                    ->icon(Heroicon::OutlinedBeaker)
                    ->schema([
                        Repeater::make('items')
                            ->label('Medicamentos')
                            ->relationship('items')
                            ->schema([
                                TextInput::make('medicine_name')
                                    ->label('Medicamento')
                                    ->required()
                                    ->placeholder('Nombre del medicamento')
                                    ->maxLength(255),

                                TextInput::make('dosage')
                                    ->label('Dosis')
                                    ->placeholder('Ej. 500 mg')
                                    ->maxLength(100),

                                TextInput::make('frequency')
                                    ->label('Frecuencia')
                                    ->placeholder('Ej. cada 8 horas')
                                    ->maxLength(100),

                                TextInput::make('duration')
                                    ->label('Duración')
                                    ->placeholder('Ej. 7 días')
                                    ->maxLength(100),

                                Textarea::make('instructions')
                                    ->label('Instrucciones')
                                    ->placeholder('Indicaciones específicas del medicamento')
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->minItems(1)
                            ->addActionLabel('Agregar medicamento')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
