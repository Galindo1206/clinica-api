<?php

namespace App\Filament\Resources\Doctors\Pages;

use App\Filament\Resources\Doctors\DoctorResource;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EditDoctor extends EditRecord
{
    protected static string $resource = DoctorResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $user = $this->record->user;

        return array_merge($data, [
            'name' => $user?->name,
            'email' => $user?->email,
            'phone' => $user?->phone,
            'document_type' => $user?->document_type,
            'document_number' => $user?->document_number,
            'birth_date' => $user?->birth_date,
            'gender' => $user?->gender,
            'address' => $user?->address,
            'is_active' => $user?->is_active,
        ]);
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return DB::transaction(function () use ($record, $data) {
            $record->user->update([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'document_type' => $data['document_type'] ?? null,
                'document_number' => $data['document_number'] ?? null,
                'birth_date' => $data['birth_date'] ?? null,
                'gender' => $data['gender'] ?? null,
                'address' => $data['address'] ?? null,
                'is_active' => $data['is_active'] ?? false,
            ]);

            $record->update([
                'cmp_number' => $data['cmp_number'],
                'specialty' => $data['specialty'],
                'license_number' => $data['license_number'] ?? null,
                'professional_title' => $data['professional_title'] ?? null,
            ]);

            return $record;
        });
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->label('Ver ficha')
                ->icon(Heroicon::OutlinedDocumentMagnifyingGlass),
        ];
    }
}
