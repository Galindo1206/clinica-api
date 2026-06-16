<?php

namespace App\Filament\Resources\MedicalDocuments\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class MedicalDocumentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Ficha del documento')
                    ->description('Metadata visible del documento médico.')
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->columns(3)
                    ->schema([
                        TextEntry::make('title')
                            ->label('Título')
                            ->icon(Heroicon::OutlinedDocument)
                            ->weight('bold')
                            ->columnSpan(2),

                        TextEntry::make('is_private')
                            ->label('Privacidad')
                            ->badge()
                            ->formatStateUsing(fn (bool $state): string => $state ? 'Privado' : 'No privado')
                            ->color(fn (bool $state): string => $state ? 'success' : 'warning'),

                        TextEntry::make('patient.user.name')
                            ->label('Paciente')
                            ->icon(Heroicon::OutlinedUser)
                            ->placeholder('No registrado'),

                        TextEntry::make('document_type')
                            ->label('Tipo')
                            ->badge()
                            ->color('info'),

                        TextEntry::make('document_date')
                            ->label('Fecha del documento')
                            ->icon(Heroicon::OutlinedCalendar)
                            ->date('d/m/Y')
                            ->placeholder('No registrada'),

                        TextEntry::make('description')
                            ->label('Descripción')
                            ->placeholder('No registrada')
                            ->columnSpanFull(),
                    ]),

                Section::make('Archivo / metadata')
                    ->description('No se muestra la ruta interna del storage privado.')
                    ->icon(Heroicon::OutlinedFolder)
                    ->columns(3)
                    ->schema([
                        TextEntry::make('file_name')
                            ->label('Archivo')
                            ->icon(Heroicon::OutlinedDocument)
                            ->copyable()
                            ->placeholder('No registrado'),

                        TextEntry::make('file_mime_type')
                            ->label('Tipo MIME')
                            ->placeholder('No registrado'),

                        TextEntry::make('file_size')
                            ->label('Tamaño')
                            ->formatStateUsing(fn ($state): string => filled($state)
                                ? number_format(((int) $state) / 1024, 1) . ' KB'
                                : 'No registrado'),

                        TextEntry::make('uploadedBy.name')
                            ->label('Subido por')
                            ->icon(Heroicon::OutlinedUserCircle)
                            ->placeholder('No registrado'),

                        TextEntry::make('created_at')
                            ->label('Fecha de subida')
                            ->icon(Heroicon::OutlinedCalendarDays)
                            ->dateTime('d/m/Y H:i'),
                    ]),
            ]);
    }
}
