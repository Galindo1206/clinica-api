<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class UpcomingAppointments extends TableWidget
{
    protected static ?string $heading = 'Próximas citas';

    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Appointment::query()
                ->with(['patient.user', 'doctor.user'])
                ->where('scheduled_at', '>=', now())
                ->whereIn('status', ['pending', 'confirmed'])
                ->orderBy('scheduled_at')
                ->limit(8))
            ->columns([
                TextColumn::make('scheduled_at')
                    ->label('Fecha/hora')
                    ->dateTime('d/m/Y H:i'),

                TextColumn::make('patient.user.name')
                    ->label('Paciente')
                    ->placeholder('No registrado'),

                TextColumn::make('doctor.user.name')
                    ->label('Médico')
                    ->placeholder('No registrado'),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => [
                        'pending' => 'Pendiente',
                        'confirmed' => 'Confirmada',
                    ][$state] ?? 'Sin estado')
                    ->color(fn (?string $state): string => [
                        'pending' => 'warning',
                        'confirmed' => 'info',
                    ][$state] ?? 'gray'),
            ])
            ->paginated(false);
    }
}
