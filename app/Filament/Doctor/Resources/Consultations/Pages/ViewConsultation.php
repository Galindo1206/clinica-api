<?php

namespace App\Filament\Doctor\Resources\Consultations\Pages;

use App\Filament\Doctor\Resources\Consultations\ConsultationResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewConsultation extends ViewRecord
{
    protected static string $resource = ConsultationResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()->label('Editar consulta')];
    }
}
