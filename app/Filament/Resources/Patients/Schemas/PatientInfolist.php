<?php

namespace App\Filament\Resources\Patients\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class PatientInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Ficha del paciente')
                    ->description('Resumen de identificación y contacto del paciente.')
                    ->icon(Heroicon::OutlinedUserCircle)
                    ->columns(3)
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('Paciente')
                            ->icon(Heroicon::OutlinedUser)
                            ->weight('bold')
                            ->columnSpan(2),

                        TextEntry::make('created_at')
                            ->label('Registrado')
                            ->icon(Heroicon::OutlinedCalendarDays)
                            ->dateTime('d/m/Y H:i'),

                        TextEntry::make('user.email')
                            ->label('Correo')
                            ->icon(Heroicon::OutlinedEnvelope)
                            ->copyable()
                            ->placeholder('No registrado'),

                        TextEntry::make('user.phone')
                            ->label('Teléfono')
                            ->icon(Heroicon::OutlinedPhone)
                            ->placeholder('No registrado'),

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

                        TextEntry::make('user.address')
                            ->label('Dirección')
                            ->icon(Heroicon::OutlinedMapPin)
                            ->placeholder('No registrada')
                            ->columnSpanFull(),
                    ]),

                Section::make('Datos médicos')
                    ->description('Información clínica rápida para admisión y atención.')
                    ->icon(Heroicon::OutlinedHeart)
                    ->columns(3)
                    ->schema([
                        TextEntry::make('blood_type')
                            ->label('Tipo de sangre')
                            ->badge()
                            ->color('danger')
                            ->placeholder('No registrado'),

                        TextEntry::make('insurance_name')
                            ->label('Seguro médico')
                            ->icon(Heroicon::OutlinedShieldCheck)
                            ->placeholder('No registrado'),

                        TextEntry::make('insurance_number')
                            ->label('Número de seguro')
                            ->icon(Heroicon::OutlinedDocumentCheck)
                            ->copyable()
                            ->placeholder('No registrado'),
                    ]),

                Section::make('Contacto de emergencia')
                    ->description('Datos de contacto ante una eventualidad.')
                    ->icon(Heroicon::OutlinedPhone)
                    ->columns(2)
                    ->schema([
                        TextEntry::make('emergency_contact_name')
                            ->label('Contacto')
                            ->icon(Heroicon::OutlinedUserGroup)
                            ->placeholder('No registrado'),

                        TextEntry::make('emergency_contact_phone')
                            ->label('Teléfono de emergencia')
                            ->icon(Heroicon::OutlinedPhoneArrowUpRight)
                            ->copyable()
                            ->placeholder('No registrado'),
                    ]),
            ]);
    }
}
