<?php

namespace App\Filament\Doctor\Resources\Prescriptions\Pages;

use App\Filament\Doctor\Resources\Prescriptions\PrescriptionResource;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPrescription extends EditRecord
{
    protected static string $resource = PrescriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [ViewAction::make()->label('Ver receta')];
    }
}
