<?php

namespace App\Filament\Doctor\Resources\MedicalDocuments\Pages;

use App\Filament\Doctor\Resources\MedicalDocuments\MedicalDocumentResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewMedicalDocument extends ViewRecord
{
    protected static string $resource = MedicalDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()->label('Editar documento')];
    }
}
