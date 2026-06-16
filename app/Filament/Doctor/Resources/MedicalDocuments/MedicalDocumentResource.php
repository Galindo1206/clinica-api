<?php

namespace App\Filament\Doctor\Resources\MedicalDocuments;

use App\Filament\Doctor\Resources\MedicalDocuments\Pages\CreateMedicalDocument;
use App\Filament\Doctor\Resources\MedicalDocuments\Pages\EditMedicalDocument;
use App\Filament\Doctor\Resources\MedicalDocuments\Pages\ListMedicalDocuments;
use App\Filament\Doctor\Resources\MedicalDocuments\Pages\ViewMedicalDocument;
use App\Filament\Doctor\Resources\Patients\PatientResource;
use App\Models\MedicalDocument;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
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

class MedicalDocumentResource extends Resource
{
    protected static ?string $model = MedicalDocument::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFolder;

    protected static ?string $navigationLabel = 'Documentos';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Documento')
                ->columns(2)
                ->schema([
                    Select::make('patient_id')
                        ->label('Paciente')
                        ->options(fn () => PatientResource::getEloquentQuery()->get()->mapWithKeys(fn ($patient) => [
                            $patient->id => $patient->user?->name ?? "Paciente #{$patient->id}",
                        ])->all())
                        ->searchable()
                        ->preload()
                        ->required(),
                    TextInput::make('title')->label('Título')->required()->maxLength(255),
                    TextInput::make('document_type')->label('Tipo')->required()->maxLength(100),
                    DatePicker::make('document_date')->label('Fecha del documento'),
                    Textarea::make('description')->label('Descripción')->rows(3)->columnSpanFull(),
                    FileUpload::make('file_path')
                        ->label('Archivo')
                        ->disk('local')
                        ->directory('medical-documents')
                        ->visibility('private')
                        ->storeFileNamesIn('file_name')
                        ->downloadable(false)
                        ->openable(false)
                        ->previewable(false)
                        ->maxSize(10240)
                        ->required()
                        ->columnSpanFull(),
                    TextInput::make('file_name')->label('Nombre')->disabled()->dehydrated(),
                    TextInput::make('file_mime_type')->label('MIME')->disabled()->dehydrated(),
                ]),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Documento')
                ->columns(2)
                ->schema([
                    TextEntry::make('title')->label('Título'),
                    TextEntry::make('patient.user.name')->label('Paciente'),
                    TextEntry::make('document_type')->label('Tipo'),
                    TextEntry::make('document_date')->label('Fecha')->date('d/m/Y')->placeholder('No registrada'),
                    TextEntry::make('file_name')->label('Archivo')->placeholder('No registrado'),
                    TextEntry::make('description')->label('Descripción')->columnSpanFull()->placeholder('No registrada'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('patient.user.name')->label('Paciente')->searchable(),
                TextColumn::make('title')->label('Título')->searchable(),
                TextColumn::make('document_type')->label('Tipo')->badge(),
                TextColumn::make('document_date')->label('Fecha')->date('d/m/Y')->sortable(),
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
            'index' => ListMedicalDocuments::route('/'),
            'create' => CreateMedicalDocument::route('/create'),
            'view' => ViewMedicalDocument::route('/{record}'),
            'edit' => EditMedicalDocument::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['patient.user', 'uploadedBy']);
        $user = auth()->user();
        $doctorId = $user?->doctor?->id;

        if (! $doctorId) {
            return $query;
        }

        return $query->where(function (Builder $query) use ($user, $doctorId): void {
            $query->where('uploaded_by_user_id', $user->id)
                ->orWhereHas('patient.accessPermissions', function (Builder $permissions) use ($doctorId): void {
                    $permissions->where('doctor_id', $doctorId)
                        ->where('is_active', true)
                        ->where(fn (Builder $q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
                        ->where(fn (Builder $q) => $q->whereNull('expires_at')->orWhere('expires_at', '>=', now()));
                });
        });
    }
}
