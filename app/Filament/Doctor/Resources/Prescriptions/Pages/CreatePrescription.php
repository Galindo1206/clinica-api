<?php

namespace App\Filament\Doctor\Resources\Prescriptions\Pages;

use App\Filament\Doctor\Resources\Prescriptions\PrescriptionResource;
use App\Models\Consultation;
use App\Models\Prescription;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreatePrescription extends CreateRecord
{
    protected static string $resource = PrescriptionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $consultation = Consultation::findOrFail($data['consultation_id']);

        $data['patient_id'] = $consultation->patient_id;
        $data['doctor_id'] = $consultation->doctor_id;
        $data['prescription_code'] = 'RX-' . str_pad((Prescription::max('id') ?? 0) + 1, 6, '0', STR_PAD_LEFT);

        return $data;
    }
}
