<?php

namespace App\Filament\Reception\Resources\Patients\Pages;

use App\Filament\Reception\Resources\Patients\PatientResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPatients extends ListRecords
{
    protected static string $resource = PatientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Nuevo paciente'),
        ];
    }
}
