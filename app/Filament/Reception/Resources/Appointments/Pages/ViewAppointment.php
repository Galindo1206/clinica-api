<?php

namespace App\Filament\Reception\Resources\Appointments\Pages;

use App\Filament\Reception\Resources\Appointments\AppointmentResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAppointment extends ViewRecord
{
    protected static string $resource = AppointmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()->label('Reprogramar / editar'),
        ];
    }
}
