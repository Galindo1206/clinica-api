<?php

namespace App\Filament\Resources\MedicalDocuments\Schemas;

use App\Models\Patient;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class MedicalDocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del documento')
                    ->description('Datos descriptivos para identificar el documento dentro de la bóveda médica.')
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->label('Título')
                            ->placeholder('Ej. Informe de laboratorio')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('document_type')
                            ->label('Tipo de documento')
                            ->placeholder('Ej. Laboratorio, Imagen, Informe, Consentimiento')
                            ->required()
                            ->maxLength(100),

                        DatePicker::make('document_date')
                            ->label('Fecha del documento')
                            ->placeholder('Selecciona una fecha'),

                        Toggle::make('is_private')
                            ->label('Documento privado')
                            ->helperText('Los documentos se mantienen en storage privado.')
                            ->default(true),

                        Textarea::make('description')
                            ->label('Descripción')
                            ->placeholder('Resumen o detalle del documento')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),

                Section::make('Paciente')
                    ->description('Paciente al que pertenece este documento médico.')
                    ->icon(Heroicon::OutlinedUser)
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
                    ]),

                Section::make('Archivo / metadata')
                    ->description('El archivo se guarda en el disco local privado. No se genera URL pública.')
                    ->icon(Heroicon::OutlinedFolder)
                    ->columns(2)
                    ->schema([
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

                        TextInput::make('file_name')
                            ->label('Nombre del archivo')
                            ->disabled()
                            ->dehydrated()
                            ->placeholder('Se completa al cargar el archivo')
                            ->maxLength(255),

                        TextInput::make('file_mime_type')
                            ->label('Tipo MIME')
                            ->disabled()
                            ->dehydrated()
                            ->placeholder('Se completa automáticamente')
                            ->maxLength(255),
                    ]),
            ]);
    }
}
