<?php

namespace App\Filament\Doctor\Resources\Prescriptions;

use App\Filament\Doctor\Resources\Prescriptions\Pages\CreatePrescription;
use App\Filament\Doctor\Resources\Prescriptions\Pages\EditPrescription;
use App\Filament\Doctor\Resources\Prescriptions\Pages\ListPrescriptions;
use App\Filament\Doctor\Resources\Prescriptions\Pages\ViewPrescription;
use App\Models\Consultation;
use App\Models\Prescription;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
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

class PrescriptionResource extends Resource
{
    protected static ?string $model = Prescription::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $navigationLabel = 'Recetas';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Receta')
                ->schema([
                    Select::make('consultation_id')
                        ->label('Consulta')
                        ->options(fn () => self::consultationOptions())
                        ->searchable()
                        ->preload()
                        ->required(),
                    DateTimePicker::make('issued_at')->label('Fecha de emisión')->seconds(false)->default(now())->required(),
                    Textarea::make('general_indications')->label('Indicaciones generales')->rows(3)->columnSpanFull(),
                ]),
            Section::make('Medicamentos')
                ->schema([
                    Repeater::make('items')
                        ->relationship('items')
                        ->schema([
                            TextInput::make('medicine_name')->label('Medicamento')->required()->maxLength(255),
                            TextInput::make('dosage')->label('Dosis')->maxLength(100),
                            TextInput::make('frequency')->label('Frecuencia')->maxLength(100),
                            TextInput::make('duration')->label('Duración')->maxLength(100),
                            Textarea::make('instructions')->label('Instrucciones')->rows(2)->columnSpanFull(),
                        ])
                        ->columns(2)
                        ->minItems(1)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Receta')
                ->columns(2)
                ->schema([
                    TextEntry::make('prescription_code')->label('Código'),
                    TextEntry::make('patient.user.name')->label('Paciente'),
                    TextEntry::make('issued_at')->label('Fecha')->dateTime('d/m/Y H:i'),
                    TextEntry::make('general_indications')->label('Indicaciones')->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('issued_at', 'desc')
            ->columns([
                TextColumn::make('prescription_code')->label('Código')->searchable(),
                TextColumn::make('patient.user.name')->label('Paciente')->searchable(),
                TextColumn::make('issued_at')->label('Fecha')->dateTime('d/m/Y H:i')->sortable(),
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
            'index' => ListPrescriptions::route('/'),
            'create' => CreatePrescription::route('/create'),
            'view' => ViewPrescription::route('/{record}'),
            'edit' => EditPrescription::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['patient.user', 'doctor.user', 'consultation', 'items']);
        $doctorId = auth()->user()?->doctor?->id;

        return $doctorId ? $query->where('doctor_id', $doctorId) : $query;
    }

    public static function consultationOptions(): array
    {
        $query = Consultation::with('patient.user')->latest('consultation_date');
        $doctorId = auth()->user()?->doctor?->id;

        if ($doctorId) {
            $query->where('doctor_id', $doctorId);
        }

        return $query->get()
            ->mapWithKeys(fn (Consultation $consultation) => [
                $consultation->id => sprintf(
                    '#%s - %s (%s)',
                    $consultation->id,
                    $consultation->patient?->user?->name ?? 'Paciente',
                    $consultation->consultation_date?->format('d/m/Y H:i') ?? 'sin fecha',
                ),
            ])
            ->all();
    }
}
