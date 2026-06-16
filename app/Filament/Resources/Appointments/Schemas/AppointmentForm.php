<?php

namespace App\Filament\Resources\Appointments\Schemas;

use App\Models\Doctor;
use App\Models\Patient;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class AppointmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Participantes')
                    ->description('Paciente y médico asignados a la cita.')
                    ->icon(Heroicon::OutlinedUserGroup)
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
                    ]),

                Section::make('Programación')
                    ->description('Fecha, hora y duración administrativa de la cita.')
                    ->icon(Heroicon::OutlinedCalendarDays)
                    ->columns(2)
                    ->schema([
                        DateTimePicker::make('scheduled_at')
                            ->label('Fecha y hora')
                            ->required()
                            ->seconds(false)
                            ->placeholder('Selecciona fecha y hora'),

                        TextInput::make('duration_minutes')
                            ->label('Duración')
                            ->numeric()
                            ->required()
                            ->default(30)
                            ->minValue(15)
                            ->maxValue(180)
                            ->suffix('minutos'),
                    ]),

                Section::make('Detalle')
                    ->description('Motivo y notas internas de la cita.')
                    ->icon(Heroicon::OutlinedClipboardDocument)
                    ->schema([
                        TextInput::make('reason')
                            ->label('Motivo')
                            ->placeholder('Ej. Control médico, dolor abdominal, revisión de resultados')
                            ->required()
                            ->maxLength(255),

                        Textarea::make('notes')
                            ->label('Notas')
                            ->placeholder('Notas adicionales de la cita')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),

                Section::make('Estado')
                    ->description('Estado administrativo de la cita.')
                    ->icon(Heroicon::OutlinedShieldCheck)
                    ->columns(2)
                    ->schema([
                        Select::make('status')
                            ->label('Estado')
                            ->options([
                                'pending' => 'Pendiente',
                                'confirmed' => 'Confirmada',
                                'cancelled' => 'Cancelada',
                                'rejected' => 'Rechazada',
                                'completed' => 'Completada',
                                'no_show' => 'No asistió',
                            ])
                            ->default('pending')
                            ->required(),

                        Textarea::make('status_reason')
                            ->label('Motivo de estado')
                            ->placeholder('Razón de cancelación, rechazo u observación administrativa')
                            ->rows(3),
                    ]),
            ]);
    }
}
