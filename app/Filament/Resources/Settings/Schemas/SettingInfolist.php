<?php

namespace App\Filament\Resources\Settings\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class SettingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Ficha de configuración')
                    ->description('Datos generales de la clínica configurada.')
                    ->icon(Heroicon::OutlinedBuildingOffice)
                    ->columns(3)
                    ->schema([
                        TextEntry::make('business_name')
                            ->label('Nombre de clínica')
                            ->icon(Heroicon::OutlinedBuildingOffice)
                            ->weight('bold'),

                        TextEntry::make('ruc')
                            ->label('RUC / identificación fiscal')
                            ->placeholder('No registrado')
                            ->copyable(),

                        TextEntry::make('updated_at')
                            ->label('Última actualización')
                            ->icon(Heroicon::OutlinedCalendarDays)
                            ->dateTime('d/m/Y H:i'),

                        TextEntry::make('phone')
                            ->label('Teléfono')
                            ->icon(Heroicon::OutlinedPhone)
                            ->placeholder('No registrado'),

                        TextEntry::make('email')
                            ->label('Email')
                            ->icon(Heroicon::OutlinedEnvelope)
                            ->copyable()
                            ->placeholder('No registrado'),

                        TextEntry::make('address')
                            ->label('Dirección')
                            ->icon(Heroicon::OutlinedMapPin)
                            ->placeholder('No registrada')
                            ->columnSpanFull(),
                    ]),

                Section::make('Branding')
                    ->description('Colores y textos usados en la identidad visual.')
                    ->icon(Heroicon::OutlinedPaintBrush)
                    ->columns(3)
                    ->schema([
                        TextEntry::make('logo_path')
                            ->label('Logo')
                            ->placeholder('No registrado'),

                        TextEntry::make('primary_color')
                            ->label('Color primario')
                            ->badge()
                            ->copyable(),

                        TextEntry::make('secondary_color')
                            ->label('Color secundario')
                            ->badge()
                            ->copyable(),

                        TextEntry::make('footer_text')
                            ->label('Pie de documento')
                            ->placeholder('No registrado')
                            ->columnSpanFull(),
                    ]),

                Section::make('Configuración operativa')
                    ->description('Opciones usadas en documentos generados.')
                    ->icon(Heroicon::OutlinedQrCode)
                    ->columns(3)
                    ->schema([
                        IconEntry::make('show_qr')
                            ->label('Mostrar QR')
                            ->boolean(),

                        IconEntry::make('show_signature')
                            ->label('Mostrar firma')
                            ->boolean(),

                        IconEntry::make('show_cmp')
                            ->label('Mostrar CMP')
                            ->boolean(),
                    ]),
            ]);
    }
}
