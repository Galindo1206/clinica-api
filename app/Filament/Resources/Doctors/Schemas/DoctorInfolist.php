<?php

namespace App\Filament\Resources\Doctors\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class DoctorInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Ficha profesional')
                    ->description('Resumen de identificación, contacto y estado del médico.')
                    ->icon(Heroicon::OutlinedUserCircle)
                    ->columns(3)
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('Médico')
                            ->icon(Heroicon::OutlinedUser)
                            ->weight('bold')
                            ->columnSpan(2),

                        TextEntry::make('user.is_active')
                            ->label('Estado')
                            ->badge()
                            ->formatStateUsing(fn (bool $state): string => $state ? 'Activo' : 'Inactivo')
                            ->color(fn (bool $state): string => $state ? 'success' : 'danger'),

                        TextEntry::make('user.email')
                            ->label('Correo')
                            ->icon(Heroicon::OutlinedEnvelope)
                            ->copyable()
                            ->placeholder('No registrado'),

                        TextEntry::make('user.phone')
                            ->label('Teléfono')
                            ->icon(Heroicon::OutlinedPhone)
                            ->placeholder('No registrado'),

                        TextEntry::make('created_at')
                            ->label('Registrado')
                            ->icon(Heroicon::OutlinedCalendarDays)
                            ->dateTime('d/m/Y H:i'),

                        TextEntry::make('user.document_type')
                            ->label('Tipo de documento')
                            ->icon(Heroicon::OutlinedIdentification)
                            ->placeholder('No registrado'),

                        TextEntry::make('user.document_number')
                            ->label('Número de documento')
                            ->icon(Heroicon::OutlinedDocumentText)
                            ->copyable()
                            ->placeholder('No registrado'),

                        TextEntry::make('user.birth_date')
                            ->label('Fecha de nacimiento')
                            ->icon(Heroicon::OutlinedCalendar)
                            ->date('d/m/Y')
                            ->placeholder('No registrada'),

                        TextEntry::make('user.gender')
                            ->label('Sexo')
                            ->badge()
                            ->formatStateUsing(fn (?string $state): string => match ($state) {
                                'male' => 'Masculino',
                                'female' => 'Femenino',
                                'other' => 'Otro',
                                default => 'No registrado',
                            })
                            ->color(fn (?string $state): string => match ($state) {
                                'male' => 'info',
                                'female' => 'danger',
                                'other' => 'gray',
                                default => 'gray',
                            }),

                        TextEntry::make('user.address')
                            ->label('Dirección')
                            ->icon(Heroicon::OutlinedMapPin)
                            ->placeholder('No registrada')
                            ->columnSpanFull(),
                    ]),

                Section::make('Datos profesionales')
                    ->description('Credenciales visibles en la gestión clínica del médico.')
                    ->icon(Heroicon::OutlinedBriefcase)
                    ->columns(2)
                    ->schema([
                        TextEntry::make('specialty')
                            ->label('Especialidad')
                            ->badge()
                            ->color('info'),

                        TextEntry::make('cmp_number')
                            ->label('CMP')
                            ->icon(Heroicon::OutlinedIdentification)
                            ->copyable(),

                        TextEntry::make('license_number')
                            ->label('RNE')
                            ->icon(Heroicon::OutlinedDocumentCheck)
                            ->copyable()
                            ->placeholder('No registrado'),

                        TextEntry::make('professional_title')
                            ->label('Título profesional')
                            ->icon(Heroicon::OutlinedAcademicCap)
                            ->placeholder('No registrado'),
                    ]),
            ]);
    }
}
