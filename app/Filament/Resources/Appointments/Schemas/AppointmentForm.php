<?php

namespace App\Filament\Resources\Appointments\Schemas;

use App\Models\Doctor;
use App\Models\Patient;
use App\Services\AvailabilityService;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
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
                            ->live()
                            ->afterStateUpdated(function (Set $set): void {
                                $set('appointment_date', null);
                                $set('available_time', null);
                                $set('scheduled_at', null);
                            })
                            ->required()
                            ->placeholder('Selecciona un médico'),
                    ]),

                Section::make('Programación')
                    ->description('Selecciona un médico, fecha y horario disponible.')
                    ->icon(Heroicon::OutlinedCalendarDays)
                    ->columns(2)
                    ->schema([
                        DatePicker::make('appointment_date')
                            ->label('Fecha')
                            ->placeholder('Selecciona una fecha')
                            ->native(false)
                            ->live()
                            ->dehydrated(false)
                            ->visible(fn (string $operation): bool => $operation === 'create')
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->afterStateUpdated(function (Set $set): void {
                                $set('available_time', null);
                                $set('scheduled_at', null);
                            }),

                        Select::make('available_time')
                            ->label('Horario disponible')
                            ->placeholder('Selecciona fecha y médico')
                            ->options(fn (Get $get): array => self::availableTimeOptions($get))
                            ->searchable()
                            ->live()
                            ->dehydrated(false)
                            ->visible(fn (string $operation): bool => $operation === 'create')
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->disabled(fn (Get $get): bool => blank($get('doctor_id')) || blank($get('appointment_date')))
                            ->afterStateUpdated(function (?string $state, Get $get, Set $set): void {
                                if (blank($state) || blank($get('appointment_date'))) {
                                    $set('scheduled_at', null);

                                    return;
                                }

                                $set(
                                    'scheduled_at',
                                    Carbon::parse(Carbon::parse($get('appointment_date'))->format('Y-m-d') . ' ' . $state)->toDateTimeString(),
                                );
                            }),

                        DateTimePicker::make('scheduled_at')
                            ->label('Fecha y hora')
                            ->hidden(fn (string $operation): bool => $operation === 'create')
                            ->dehydrated()
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

    private static function availableTimeOptions(Get $get): array
    {
        if (blank($get('doctor_id')) || blank($get('appointment_date'))) {
            return [];
        }

        $doctor = Doctor::find($get('doctor_id'));

        if (! $doctor) {
            return [];
        }

        $availability = AvailabilityService::getDoctorAvailability(
            $doctor,
            Carbon::parse($get('appointment_date'))->format('Y-m-d'),
        );

        return collect($availability['slots'])
            ->filter(fn (array $slot): bool => $slot['available'])
            ->mapWithKeys(fn (array $slot): array => [
                $slot['time'] => $slot['time'],
            ])
            ->all();
    }
}
