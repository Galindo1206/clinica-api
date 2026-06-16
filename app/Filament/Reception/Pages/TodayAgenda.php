<?php

namespace App\Filament\Reception\Pages;

use App\Models\Appointment;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TodayAgenda extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.reception.pages.today-agenda';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendar;

    protected static ?string $navigationLabel = 'Agenda de Hoy';

    protected static ?string $title = 'Agenda de Hoy';

    protected static ?int $navigationSort = 3;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Appointment::query()
                ->with(['patient.user', 'doctor.user'])
                ->whereDate('scheduled_at', today())
                ->orderBy('scheduled_at'))
            ->columns([
                TextColumn::make('scheduled_at')
                    ->label('Hora')
                    ->time('H:i')
                    ->sortable(),

                TextColumn::make('patient.user.name')
                    ->label('Paciente')
                    ->searchable()
                    ->placeholder('No registrado'),

                TextColumn::make('doctor.user.name')
                    ->label('Médico')
                    ->searchable()
                    ->placeholder('No registrado'),

                TextColumn::make('reason')
                    ->label('Motivo')
                    ->limit(45)
                    ->wrap()
                    ->placeholder('No registrado'),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => self::statusLabels()[$state] ?? 'Sin estado')
                    ->color(fn (?string $state): string => self::statusColors()[$state] ?? 'gray'),
            ])
            ->emptyStateHeading('No hay citas programadas para hoy')
            ->paginated(false);
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
