<?php

namespace App\Filament\Reception\Resources\Patients\Pages;

use App\Filament\Reception\Resources\Patients\PatientResource;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EditPatient extends EditRecord
{
    protected static string $resource = PatientResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return array_merge($data, [
            'name' => $this->record->user?->name,
            'email' => $this->record->user?->email,
            'phone' => $this->record->user?->phone,
            'document_type' => $this->record->user?->document_type,
            'document_number' => $this->record->user?->document_number,
            'birth_date' => $this->record->user?->birth_date,
        ]);
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return DB::transaction(function () use ($record, $data): Model {
            $record->user->update([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'document_type' => $data['document_type'] ?? null,
                'document_number' => $data['document_number'] ?? null,
                'birth_date' => $data['birth_date'] ?? null,
            ]);

            return $record;
        });
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()->label('Ver ficha'),
        ];
    }
}
