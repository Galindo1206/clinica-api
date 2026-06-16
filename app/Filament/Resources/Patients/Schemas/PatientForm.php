<?php

namespace App\Filament\Resources\Patients\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class PatientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Datos personales')
                    ->description('Información de identificación y contacto principal del paciente.')
                    ->icon(Heroicon::OutlinedIdentification)
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre completo')
                            ->placeholder('Ej. Ana María Torres')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('Correo electrónico')
                            ->email()
                            ->placeholder('paciente@correo.com')
                            ->required()
                            ->rule(function ($record) {
                                return \Illuminate\Validation\Rule::unique('users', 'email')
                                    ->ignore($record?->user?->id);
                            })
                            ->maxLength(255),

                        TextInput::make('phone')
                            ->label('Teléfono')
                            ->tel()
                            ->placeholder('Ej. 999 999 999')
                            ->maxLength(20),

                        Select::make('document_type')
                            ->label('Tipo de documento')
                            ->placeholder('Selecciona un tipo')
                            ->options([
                                'DNI' => 'DNI',
                                'CE' => 'Carné de extranjería',
                                'PASSPORT' => 'Pasaporte',
                            ]),

                        TextInput::make('document_number')
                            ->label('Número de documento')
                            ->placeholder('Ej. 12345678')
                            ->maxLength(30),

                        DatePicker::make('birth_date')
                            ->label('Fecha de nacimiento')
                            ->placeholder('Selecciona una fecha'),

                        Select::make('gender')
                            ->label('Sexo')
                            ->placeholder('Selecciona una opción')
                            ->options([
                                'male' => 'Masculino',
                                'female' => 'Femenino',
                                'other' => 'Otro',
                            ]),

                        TextInput::make('address')
                            ->label('Dirección')
                            ->placeholder('Dirección del paciente')
                            ->columnSpanFull()
                            ->maxLength(255),
                    ]),

                Section::make('Datos médicos')
                    ->description('Datos clínicos de referencia para atención rápida.')
                    ->icon(Heroicon::OutlinedHeart)
                    ->columns(2)
                    ->schema([
                        Select::make('blood_type')
                            ->label('Tipo de sangre')
                            ->placeholder('No registrado')
                            ->options([
                                'A+' => 'A+',
                                'A-' => 'A-',
                                'B+' => 'B+',
                                'B-' => 'B-',
                                'AB+' => 'AB+',
                                'AB-' => 'AB-',
                                'O+' => 'O+',
                                'O-' => 'O-',
                            ]),
                    ]),

                Section::make('Seguro médico')
                    ->description('Información de cobertura o póliza asociada al paciente.')
                    ->icon(Heroicon::OutlinedShieldCheck)
                    ->columns(2)
                    ->schema([
                        TextInput::make('insurance_name')
                            ->label('Seguro médico')
                            ->placeholder('Ej. Seguro Integral, EPS, privado')
                            ->maxLength(255),

                        TextInput::make('insurance_number')
                            ->label('Número de seguro')
                            ->placeholder('Código o número de afiliación')
                            ->maxLength(255),
                    ]),

                Section::make('Contacto de emergencia')
                    ->description('Persona a contactar ante una eventualidad durante la atención.')
                    ->icon(Heroicon::OutlinedPhone)
                    ->columns(2)
                    ->schema([
                        TextInput::make('emergency_contact_name')
                            ->label('Nombre del contacto')
                            ->placeholder('Nombre completo')
                            ->maxLength(255),

                        TextInput::make('emergency_contact_phone')
                            ->label('Teléfono del contacto')
                            ->tel()
                            ->placeholder('Ej. 999 999 999')
                            ->maxLength(20),
                    ]),
            ]);
    }
}
