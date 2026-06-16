<?php

namespace App\Filament\Doctor\Resources\Consultations\Pages;

use App\Filament\Doctor\Resources\Consultations\ConsultationResource;
use App\Models\Consultation;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateConsultation extends CreateRecord
{
    protected static string $resource = ConsultationResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data): Consultation {
            $vitals = $data['vitals'] ?? [];
            unset($data['vitals']);

            $data['doctor_id'] = auth()->user()?->doctor?->id ?? $data['doctor_id'] ?? null;

            $consultation = Consultation::create($data);

            if (array_filter($vitals, fn ($value) => filled($value))) {
                $consultation->vitals()->create($vitals);
            }

            return $consultation;
        });
    }
}
