<?php

namespace App\Filament\Doctor\Resources\MedicalDocuments\Pages;

use App\Filament\Doctor\Resources\MedicalDocuments\MedicalDocumentResource;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditMedicalDocument extends EditRecord
{
    protected static string $resource = MedicalDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [ViewAction::make()->label('Ver documento')];
    }
}
