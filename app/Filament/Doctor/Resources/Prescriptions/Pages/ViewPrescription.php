<?php

namespace App\Filament\Doctor\Resources\Prescriptions\Pages;

use App\Filament\Doctor\Resources\Prescriptions\PrescriptionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPrescription extends ViewRecord
{
    protected static string $resource = PrescriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()->label('Editar receta')];
    }
}
