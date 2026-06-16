<?php

namespace App\Filament\Resources\DoctorUnavailabilities\Pages;

use App\Filament\Resources\DoctorUnavailabilities\DoctorUnavailabilityResource;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditDoctorUnavailability extends EditRecord
{
    protected static string $resource = DoctorUnavailabilityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->label('Ver ficha')
                ->icon(Heroicon::OutlinedDocumentMagnifyingGlass),
        ];
    }
}
