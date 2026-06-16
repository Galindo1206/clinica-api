<?php

namespace App\Filament\Doctor\Resources\Appointments;

use App\Filament\Doctor\Resources\Appointments\Pages\ListAppointments;
use App\Filament\Doctor\Resources\Appointments\Pages\ViewAppointment;
use App\Models\Appointment;
use BackedEnum;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?string $navigationLabel = 'Mi Agenda';

    protected static ?string $modelLabel = 'Cita';

    protected static ?string $pluralModelLabel = 'Mi Agenda';

    protected static ?int $navigationSort = 1;

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Cita')
                ->columns(2)
                ->schema([
                    TextEntry::make('scheduled_at')->label('Fecha y hora')->dateTime('d/m/Y H:i'),
                    TextEntry::make('patient.user.name')->label('Paciente'),
                    TextEntry::make('doctor.user.name')->label('Médico'),
                    TextEntry::make('status')->label('Estado')->badge(),
                    TextEntry::make('reason')->label('Motivo')->columnSpanFull(),
                    TextEntry::make('notes')->label('Notas')->placeholder('No registradas')->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('scheduled_at', 'desc')
            ->columns([
                TextColumn::make('scheduled_at')->label('Fecha/hora')->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('patient.user.name')->label('Paciente')->searchable(),
                TextColumn::make('reason')->label('Motivo')->limit(45)->searchable(),
                TextColumn::make('status')->label('Estado')->badge()->formatStateUsing(fn (?string $state) => self::statusLabels()[$state] ?? 'Sin estado'),
            ])
            ->recordActions([
                ViewAction::make()->label('Ver'),
            ])
            ->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAppointments::route('/'),
            'view' => ViewAppointment::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['patient.user', 'doctor.user']);
        $doctorId = auth()->user()?->doctor?->id;

        return $doctorId ? $query->where('doctor_id', $doctorId) : $query;
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
}
