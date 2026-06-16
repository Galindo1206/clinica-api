<?php

namespace App\Filament\Resources\DoctorUnavailabilities\Pages;

use App\Filament\Resources\DoctorUnavailabilities\DoctorUnavailabilityResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListDoctorUnavailabilities extends ListRecords
{
    protected static string $resource = DoctorUnavailabilityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Nuevo bloqueo')
                ->icon(Heroicon::OutlinedNoSymbol),
        ];
    }
}
