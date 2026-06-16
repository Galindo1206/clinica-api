<?php

namespace App\Filament\Reception\Resources\Patients\Pages;

use App\Filament\Reception\Resources\Patients\PatientResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPatient extends ViewRecord
{
    protected static string $resource = PatientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()->label('Editar paciente'),
        ];
    }
}
