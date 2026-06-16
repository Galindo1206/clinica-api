<?php

namespace App\Filament\Resources\DoctorUnavailabilities\Pages;

use App\Filament\Resources\DoctorUnavailabilities\DoctorUnavailabilityResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewDoctorUnavailability extends ViewRecord
{
    protected static string $resource = DoctorUnavailabilityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->label('Editar bloqueo')
                ->icon(Heroicon::OutlinedPencilSquare),
        ];
    }
}
