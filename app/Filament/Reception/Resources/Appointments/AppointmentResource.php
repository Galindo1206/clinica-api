<?php

namespace App\Filament\Reception\Resources\Appointments;

use App\Filament\Reception\Resources\Appointments\Pages\CreateAppointment;
use App\Filament\Reception\Resources\Appointments\Pages\EditAppointment;
use App\Filament\Reception\Resources\Appointments\Pages\ListAppointments;
use App\Filament\Reception\Resources\Appointments\Pages\ViewAppointment;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Services\AvailabilityService;
use BackedEnum;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?string $modelLabel = 'Cita';

    protected static ?string $pluralModelLabel = 'Citas';

    protected static ?string $navigationLabel = 'Citas';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Agendar cita')
                    ->description('Selecciona paciente, médico, fecha y un horario disponible.')
                    ->columns(2)
                    ->schema([
                        Select::make('patient_id')
                            ->label('Paciente')
                            ->options(fn () => Patient::with('user')->get()->sortBy(fn (Patient $patient) => $patient->user?->name ?? '')->mapWithKeys(fn (Patient $patient) => [
                                $patient->id => $patient->user?->name ?? "Paciente #{$patient->id}",
                            ]))
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('doctor_id')
                            ->label('Médico')
                            ->options(fn () => Doctor::with('user')->get()->sortBy(fn (Doctor $doctor) => $doctor->user?->name ?? '')->mapWithKeys(fn (Doctor $doctor) => [
                                $doctor->id => $doctor->user?->name ?? "Médico #{$doctor->id}",
                            ]))
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(fn (Set $set) => self::resetSelectedSchedule($set))
                            ->required(),

                        DatePicker::make('appointment_date')
                            ->label('Fecha')
                            ->native(false)
                            ->live()
                            ->dehydrated(false)
                            ->afterStateUpdated(fn (Set $set) => self::resetSelectedSchedule($set))
                            ->required(),

                        TextInput::make('duration_minutes')
                            ->label('Duración')
                            ->numeric()
                            ->default(30)
                            ->required()
                            ->suffix('minutos'),

                        ToggleButtons::make('selected_time')
                            ->label('Horario disponible')
                            ->options(fn (Get $get): array => self::availableTimeOptions($get))
                            ->helperText(fn (Get $get): ?string => self::availabilityHelperText($get))
                            ->inline()
                            ->live()
                            ->dehydrated(false)
                            ->disabled(fn (Get $get): bool => blank($get('doctor_id')) || blank($get('appointment_date')) || self::availableTimeOptions($get) === [])
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->afterStateUpdated(function (?string $state, Get $get, Set $set): void {
                                if (blank($state) || blank($get('appointment_date'))) {
                                    $set('scheduled_at', null);

                                    return;
                                }

                                $date = Carbon::parse($get('appointment_date'))->format('Y-m-d');

                                $set('scheduled_at', Carbon::parse("{$date} {$state}")->toDateTimeString());
                            })
                            ->columnSpanFull(),

                        Hidden::make('scheduled_at')
                            ->dehydrated()
                            ->required(),

                        TextInput::make('reason')
                            ->label('Motivo')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Select::make('status')
                            ->label('Estado')
                            ->options(self::statusLabels())
                            ->default('confirmed')
                            ->required(),

                        Textarea::make('status_reason')
                            ->label('Motivo de estado')
                            ->rows(3),

                        Textarea::make('notes')
                            ->label('Notas')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detalle de cita')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('patient.user.name')->label('Paciente'),
                        TextEntry::make('doctor.user.name')->label('Médico'),
                        TextEntry::make('scheduled_at')->label('Fecha y hora')->dateTime('d/m/Y H:i'),
                        TextEntry::make('duration_minutes')->label('Duración')->formatStateUsing(fn ($state) => "{$state} min"),
                        TextEntry::make('status')->label('Estado')->badge()->formatStateUsing(fn (?string $state) => self::statusLabels()[$state] ?? 'Sin estado'),
                        TextEntry::make('reason')->label('Motivo')->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('scheduled_at', 'desc')
            ->columns([
                TextColumn::make('scheduled_at')->label('Fecha/hora')->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('patient.user.name')->label('Paciente')->searchable()->sortable(),
                TextColumn::make('doctor.user.name')->label('Médico')->searchable()->sortable(),
                TextColumn::make('reason')->label('Motivo')->limit(35)->searchable(),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => self::statusLabels()[$state] ?? 'Sin estado')
                    ->color(fn (?string $state) => self::statusColors()[$state] ?? 'gray'),
            ])
            ->recordActions([
                Action::make('confirm')
                    ->label('Confirmar')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->visible(fn (Appointment $record) => $record->status === 'pending')
                    ->action(fn (Appointment $record) => $record->update(['status' => 'confirmed', 'status_reason' => null])),

                Action::make('cancel')
                    ->label('Cancelar')
                    ->icon(Heroicon::OutlinedXCircle)
                    ->color('danger')
                    ->schema([
                        Textarea::make('status_reason')
                            ->label('Motivo de cancelación')
                            ->rows(3),
                    ])
                    ->action(fn (Appointment $record, array $data) => $record->update([
                        'status' => 'cancelled',
                        'status_reason' => $data['status_reason'] ?? null,
                    ])),

                ViewAction::make()->label('Ver'),
                EditAction::make()->label('Reprogramar / editar'),
            ])
            ->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAppointments::route('/'),
            'create' => CreateAppointment::route('/create'),
            'view' => ViewAppointment::route('/{record}'),
            'edit' => EditAppointment::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['patient.user', 'doctor.user']);
    }

    private static function resetSelectedSchedule(Set $set): void
    {
        $set('selected_time', null);
        $set('scheduled_at', null);
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
                $slot['time'] => Carbon::parse($slot['datetime'])->format('h:i A'),
            ])
            ->all();
    }

    private static function availabilityHelperText(Get $get): ?string
    {
        if (blank($get('doctor_id')) || blank($get('appointment_date'))) {
            return 'Selecciona médico y fecha para ver horarios disponibles.';
        }

        return self::availableTimeOptions($get) === []
            ? 'No hay horarios disponibles para este día'
            : null;
    }

    private static function statusLabels(): array
    {
        return [
            'pending' => 'Pendiente',
            'confirmed' => 'Confirmada',
            'cancelled' => 'Cancelada',
            'rejected' => 'Rechazada',
            'completed' => 'Completada',
            'no_show' => 'No asistió',
        ];
    }

    private static function statusColors(): array
    {
        return [
            'pending' => 'warning',
            'confirmed' => 'info',
            'cancelled' => 'danger',
            'rejected' => 'danger',
            'completed' => 'success',
            'no_show' => 'gray',
        ];
    }
}
