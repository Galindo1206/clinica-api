<?php

namespace App\Filament\Resources\Doctors\Tables;

use App\Filament\Resources\Doctors\DoctorResource;
use Carbon\Carbon;
use Filament\Actions\Action;
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

                TextColumn::make('schedule_days')
                    ->label('Días configurados')
                    ->state(fn ($record): string => self::configuredDays($record))
                    ->badge()
                    ->color('info')
                    ->placeholder('Sin horarios'),

                TextColumn::make('schedule_ranges')
                    ->label('Horarios configurados')
                    ->state(fn ($record): string => self::configuredRanges($record))
                    ->placeholder('Sin horarios'),

                TextColumn::make('schedule_slot_minutes')
                    ->label('Turno')
                    ->state(fn ($record): string => self::configuredSlotMinutes($record))
                    ->placeholder('Sin horarios'),

                TextColumn::make('schedule_status')
                    ->label('Horario')
                    ->state(fn ($record): string => self::scheduleStatus($record))
                    ->badge()
                    ->color(fn ($record): string => self::scheduleStatus($record) === 'Activo' ? 'success' : 'gray'),

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
                Action::make('manageSchedules')
                    ->label('Gestionar horarios')
                    ->icon(Heroicon::OutlinedClock)
                    ->url(fn ($record): string => DoctorResource::getUrl('edit', ['record' => $record])),

                ViewAction::make()
                    ->label('Ver')
                    ->icon(Heroicon::OutlinedDocumentMagnifyingGlass),
                EditAction::make()
                    ->label('Editar')
                    ->icon(Heroicon::OutlinedPencilSquare),
            ])
            ->toolbarActions([]);
    }

    private static function configuredDays($record): string
    {
        $days = $record->schedules
            ->sortBy('day_of_week')
            ->pluck('day_of_week')
            ->unique()
            ->map(fn ($day): string => self::shortDayLabels()[(int) $day] ?? 'N/D')
            ->values();

        return $days->isEmpty() ? 'Sin horarios' : $days->join(', ');
    }

    private static function configuredRanges($record): string
    {
        $ranges = $record->schedules
            ->map(fn ($schedule): string => self::formatTime($schedule->start_time) . ' - ' . self::formatTime($schedule->end_time))
            ->unique()
            ->values();

        return $ranges->isEmpty() ? 'Sin horarios' : $ranges->join(', ');
    }

    private static function configuredSlotMinutes($record): string
    {
        $slots = $record->schedules
            ->pluck('slot_minutes')
            ->filter()
            ->unique()
            ->values()
            ->map(fn ($minutes): string => "{$minutes} min");

        return $slots->isEmpty() ? 'Sin horarios' : $slots->join(', ');
    }

    private static function scheduleStatus($record): string
    {
        return $record->schedules->contains('is_active', true) ? 'Activo' : 'Sin horarios';
    }

    private static function formatTime($time): string
    {
        return Carbon::parse($time)->format('H:i');
    }

    private static function shortDayLabels(): array
    {
        return [
            0 => 'Dom',
            1 => 'Lun',
            2 => 'Mar',
            3 => 'Mié',
            4 => 'Jue',
            5 => 'Vie',
            6 => 'Sáb',
        ];
    }
}
