<?php

namespace App\Filament\Resources\Prescriptions\Pages;

use App\Filament\Resources\Prescriptions\PrescriptionResource;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditPrescription extends EditRecord
{
    protected static string $resource = PrescriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->label('Ver ficha')
                ->icon(Heroicon::OutlinedDocumentMagnifyingGlass),
            // TODO: Rehabilitar PDF cuando exista una ruta web segura compatible con la sesión de Filament.
        ];
    }
}
