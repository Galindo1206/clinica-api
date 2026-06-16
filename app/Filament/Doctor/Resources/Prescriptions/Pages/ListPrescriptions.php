<?php

namespace App\Filament\Doctor\Resources\Prescriptions\Pages;

use App\Filament\Doctor\Resources\Prescriptions\PrescriptionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPrescriptions extends ListRecords
{
    protected static string $resource = PrescriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('Nueva receta')];
    }
}
