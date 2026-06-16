<?php

namespace App\Filament\Doctor\Resources\Consultations;

use App\Filament\Doctor\Resources\Consultations\Pages\CreateConsultation;
use App\Filament\Doctor\Resources\Consultations\Pages\EditConsultation;
use App\Filament\Doctor\Resources\Consultations\Pages\ListConsultations;
use App\Filament\Doctor\Resources\Consultations\Pages\ViewConsultation;
use App\Models\Consultation;
use App\Models\Doctor;
use App\Models\Patient;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ConsultationResource extends Resource
{
    protected static ?string $model = Consultation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $navigationLabel = 'Consultas';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Consulta')
                ->columns(2)
                ->schema([
                    Select::make('patient_id')
                        ->label('Paciente')
                        ->options(fn () => self::patientOptions())
                        ->searchable()
                        ->preload()
                        ->required(),
                    Select::make('doctor_id')
                        ->label('Médico')
                        ->options(fn () => Doctor::with('user')->get()->mapWithKeys(fn (Doctor $doctor) => [
                            $doctor->id => $doctor->user?->name ?? "Médico #{$doctor->id}",
                        ]))
                        ->searchable()
                        ->preload()
                        ->visible(fn () => blank(auth()->user()?->doctor))
                        ->required(fn () => blank(auth()->user()?->doctor)),
                    DateTimePicker::make('consultation_date')->label('Fecha')->seconds(false)->default(now())->required(),
                    TextInput::make('reason')->label('Motivo')->required()->maxLength(255)->columnSpanFull(),
                    Textarea::make('symptoms')->label('Síntomas')->rows(3)->columnSpanFull(),
                    Textarea::make('diagnosis')->label('Diagnóstico')->rows(3),
                    Textarea::make('treatment')->label('Tratamiento')->rows(3),
                    Textarea::make('observations')->label('Observaciones')->rows(3)->columnSpanFull(),
                ]),
            Section::make('Signos vitales')
                ->columns(3)
                ->schema([
                    TextInput::make('vitals.weight')->label('Peso')->numeric()->suffix('kg'),
                    TextInput::make('vitals.height')->label('Talla')->numeric()->suffix('cm'),
                    TextInput::make('vitals.temperature')->label('Temperatura')->numeric()->suffix('°C'),
                    TextInput::make('vitals.heart_rate')->label('Frecuencia cardiaca')->numeric(),
                    TextInput::make('vitals.respiratory_rate')->label('Frecuencia respiratoria')->numeric(),
                    TextInput::make('vitals.oxygen_saturation')->label('Saturación O2')->numeric()->suffix('%'),
                    TextInput::make('vitals.blood_pressure')->label('Presión arterial'),
                ]),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Consulta')
                ->columns(2)
                ->schema([
                    TextEntry::make('patient.user.name')->label('Paciente'),
                    TextEntry::make('consultation_date')->label('Fecha')->dateTime('d/m/Y H:i'),
                    TextEntry::make('reason')->label('Motivo')->columnSpanFull(),
                    TextEntry::make('diagnosis')->label('Diagnóstico')->placeholder('No registrado'),
                    TextEntry::make('treatment')->label('Tratamiento')->placeholder('No registrado'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('consultation_date', 'desc')
            ->columns([
                TextColumn::make('consultation_date')->label('Fecha')->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('patient.user.name')->label('Paciente')->searchable(),
                TextColumn::make('reason')->label('Motivo')->limit(40)->searchable(),
                TextColumn::make('diagnosis')->label('Diagnóstico')->limit(40)->placeholder('No registrado'),
            ])
            ->recordActions([
                ViewAction::make()->label('Ver'),
                EditAction::make()->label('Editar'),
            ])
            ->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListConsultations::route('/'),
            'create' => CreateConsultation::route('/create'),
            'view' => ViewConsultation::route('/{record}'),
            'edit' => EditConsultation::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['patient.user', 'doctor.user', 'vitals']);
        $doctorId = auth()->user()?->doctor?->id;

        return $doctorId ? $query->where('doctor_id', $doctorId) : $query;
    }

    public static function patientOptions(): array
    {
        return \App\Filament\Doctor\Resources\Patients\PatientResource::getEloquentQuery()
            ->get()
            ->sortBy(fn (Patient $patient) => $patient->user?->name ?? '')
            ->mapWithKeys(fn (Patient $patient) => [$patient->id => $patient->user?->name ?? "Paciente #{$patient->id}"])
            ->all();
    }
}
