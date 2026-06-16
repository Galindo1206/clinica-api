<?php

namespace App\Filament\Resources\Doctors\RelationManagers;

use App\Models\DoctorSchedule;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SchedulesRelationManager extends RelationManager
{
    protected static string $relationship = 'schedules';

    protected static ?string $title = 'Horarios';

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('day_of_week')
            ->columns([
                TextColumn::make('day_of_week')
                    ->label('Día')
                    ->badge()
                    ->formatStateUsing(fn ($state): string => self::dayLabels()[(int) $state] ?? 'No registrado')
                    ->color('info')
                    ->sortable(),

                TextColumn::make('time_range')
                    ->label('Horario')
                    ->state(fn ($record): string => self::formatTime($record->start_time) . ' - ' . self::formatTime($record->end_time)),

                TextColumn::make('slot_minutes')
                    ->label('Turno')
                    ->formatStateUsing(fn ($state): string => filled($state) ? "{$state} min" : 'No registrado')
                    ->sortable(),

                TextColumn::make('is_active')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Activo' : 'Inactivo')
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger')
                    ->sortable(),
            ])
            ->headerActions([
                Action::make('createWeeklySchedule')
                    ->label('Nuevo horario')
                    ->icon(Heroicon::OutlinedClock)
                    ->schema([
                        CheckboxList::make('days')
                            ->label('Días de atención')
                            ->options(self::dayOptions())
                            ->columns(4)
                            ->required(),

                        TimePicker::make('start_time')
                            ->label('Hora inicio')
                            ->seconds(false)
                            ->required(),

                        TimePicker::make('end_time')
                            ->label('Hora fin')
                            ->seconds(false)
                            ->after('start_time')
                            ->required(),

                        Select::make('slot_minutes')
                            ->label('Duración de cada turno')
                            ->options([
                                15 => '15 minutos',
                                20 => '20 minutos',
                                30 => '30 minutos',
                                45 => '45 minutos',
                                60 => '60 minutos',
                            ])
                            ->default(30)
                            ->required(),

                        Toggle::make('is_active')
                            ->label('Horario activo')
                            ->default(true),
                    ])
                    ->action(function (array $data): void {
                        $startTime = Carbon::parse($data['start_time'])->format('H:i:s');
                        $endTime = Carbon::parse($data['end_time'])->format('H:i:s');

                        foreach ($data['days'] as $dayOfWeek) {
                            DoctorSchedule::updateOrCreate(
                                [
                                    'doctor_id' => $this->ownerRecord->id,
                                    'day_of_week' => (int) $dayOfWeek,
                                    'start_time' => $startTime,
                                    'end_time' => $endTime,
                                ],
                                [
                                    'slot_minutes' => $data['slot_minutes'],
                                    'is_active' => $data['is_active'] ?? true,
                                ],
                            );
                        }
                    })
                    ->successNotificationTitle('Horarios guardados'),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Editar')
                    ->icon(Heroicon::OutlinedPencilSquare)
                    ->schema([
                        Select::make('day_of_week')
                            ->label('Día de semana')
                            ->options(self::dayOptions())
                            ->required(),

                        TimePicker::make('start_time')
                            ->label('Hora inicio')
                            ->seconds(false)
                            ->required(),

                        TimePicker::make('end_time')
                            ->label('Hora fin')
                            ->seconds(false)
                            ->after('start_time')
                            ->required(),

                        Select::make('slot_minutes')
                            ->label('Duración de cada turno')
                            ->options([
                                15 => '15 minutos',
                                20 => '20 minutos',
                                30 => '30 minutos',
                                45 => '45 minutos',
                                60 => '60 minutos',
                            ])
                            ->required(),

                        Toggle::make('is_active')
                            ->label('Horario activo'),
                    ]),

                DeleteAction::make()
                    ->label('Eliminar')
                    ->icon(Heroicon::OutlinedTrash),
            ])
            ->toolbarActions([]);
    }

    private static function dayOptions(): array
    {
        return [
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
            0 => 'Domingo',
        ];
    }

    private static function dayLabels(): array
    {
        return [
            0 => 'Domingo',
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
        ];
    }

    private static function formatTime($time): string
    {
        return Carbon::parse($time)->format('H:i');
    }
}
