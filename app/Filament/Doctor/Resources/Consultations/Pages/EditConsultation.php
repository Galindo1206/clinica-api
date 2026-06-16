<?php

namespace App\Filament\Doctor\Resources\Consultations\Pages;

use App\Filament\Doctor\Resources\Consultations\ConsultationResource;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EditConsultation extends EditRecord
{
    protected static string $resource = ConsultationResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['vitals'] = $this->record->vitals?->only([
            'weight', 'height', 'heart_rate', 'respiratory_rate', 'temperature', 'blood_pressure', 'oxygen_saturation',
        ]) ?? [];

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return DB::transaction(function () use ($record, $data): Model {
            $vitals = $data['vitals'] ?? [];
            unset($data['vitals']);

            $record->update($data);

            if (array_filter($vitals, fn ($value) => filled($value))) {
                $record->vitals()->updateOrCreate(['consultation_id' => $record->id], $vitals);
            }

            return $record;
        });
    }

    protected function getHeaderActions(): array
    {
        return [ViewAction::make()->label('Ver consulta')];
    }
}
