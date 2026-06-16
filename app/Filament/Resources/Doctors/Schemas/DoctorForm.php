<?php

namespace App\Filament\Resources\Doctors\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Validation\Rule;

class DoctorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Datos personales')
                    ->description('Información de identificación y contacto del médico.')
                    ->icon(Heroicon::OutlinedIdentification)
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre completo')
                            ->placeholder('Ej. Luis Fernando Ramos')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('Correo electrónico')
                            ->email()
                            ->placeholder('medico@clinica.com')
                            ->required()
                            ->rule(function ($record) {
                                return Rule::unique('users', 'email')
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
                            ->placeholder('Dirección del médico')
                            ->columnSpanFull()
                            ->maxLength(255),
                    ]),

                Section::make('Datos profesionales')
                    ->description('Credenciales y especialidad usadas para agenda, atención y documentos clínicos.')
                    ->icon(Heroicon::OutlinedBriefcase)
                    ->columns(2)
                    ->schema([
                        TextInput::make('specialty')
                            ->label('Especialidad')
                            ->placeholder('Ej. Cardiología')
                            ->required()
                            ->maxLength(100),

                        TextInput::make('cmp_number')
                            ->label('CMP')
                            ->placeholder('Código de colegiatura')
                            ->required()
                            ->rule(function ($record) {
                                return Rule::unique('doctors', 'cmp_number')
                                    ->ignore($record?->id);
                            })
                            ->maxLength(50),

                        TextInput::make('license_number')
                            ->label('RNE')
                            ->placeholder('Registro nacional de especialista')
                            ->maxLength(100),

                        TextInput::make('professional_title')
                            ->label('Título profesional')
                            ->placeholder('Ej. Médico Cirujano')
                            ->maxLength(150),
                    ]),

                Section::make('Estado')
                    ->description('Controla si el médico puede acceder al panel administrativo.')
                    ->icon(Heroicon::OutlinedShieldCheck)
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Médico activo')
                            ->helperText('Un médico inactivo no podrá acceder al panel.')
                            ->default(true),
                    ]),
            ]);
    }
}
