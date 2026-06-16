<?php

namespace App\Filament\Resources\Settings\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class SettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Datos de la clínica')
                    ->description('Identidad principal de la clínica o sede configurada.')
                    ->icon(Heroicon::OutlinedBuildingOffice)
                    ->columns(2)
                    ->schema([
                        TextInput::make('business_name')
                            ->label('Nombre de clínica')
                            ->placeholder('Ej. Clínica Central')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('ruc')
                            ->label('RUC / identificación fiscal')
                            ->placeholder('Ej. 00000000000')
                            ->maxLength(20),

                        Textarea::make('address')
                            ->label('Dirección')
                            ->placeholder('Dirección fiscal o sede principal')
                            ->rows(3)
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ]),

                Section::make('Contacto')
                    ->description('Canales visibles para documentos y comunicación institucional.')
                    ->icon(Heroicon::OutlinedEnvelope)
                    ->columns(2)
                    ->schema([
                        TextInput::make('phone')
                            ->label('Teléfono')
                            ->tel()
                            ->placeholder('Ej. 999 999 999')
                            ->maxLength(50),

                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->placeholder('contacto@clinica.com')
                            ->maxLength(255),
                    ]),

                Section::make('Branding')
                    ->description('Apariencia visual usada en documentos y experiencia administrativa.')
                    ->icon(Heroicon::OutlinedPaintBrush)
                    ->columns(2)
                    ->schema([
                        FileUpload::make('logo_path')
                            ->label('Logo')
                            ->disk('public')
                            ->directory('settings/logos')
                            ->image()
                            ->maxSize(2048)
                            ->placeholder('Sube un logo')
                            ->columnSpanFull(),

                        ColorPicker::make('primary_color')
                            ->label('Color primario')
                            ->required(),

                        ColorPicker::make('secondary_color')
                            ->label('Color secundario')
                            ->required(),

                        Textarea::make('footer_text')
                            ->label('Texto de pie de documento')
                            ->placeholder('Texto legal o informativo para documentos generados')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Section::make('Configuración operativa')
                    ->description('Opciones de presentación para documentos clínicos generados.')
                    ->icon(Heroicon::OutlinedQrCode)
                    ->columns(3)
                    ->schema([
                        Toggle::make('show_qr')
                            ->label('Mostrar QR')
                            ->default(false),

                        Toggle::make('show_signature')
                            ->label('Mostrar firma')
                            ->default(false),

                        Toggle::make('show_cmp')
                            ->label('Mostrar CMP')
                            ->default(true),
                    ]),
            ]);
    }
}
