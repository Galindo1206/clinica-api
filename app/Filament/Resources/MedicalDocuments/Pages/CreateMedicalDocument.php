<?php

namespace App\Filament\Resources\MedicalDocuments\Pages;

use App\Filament\Resources\MedicalDocuments\MedicalDocumentResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;

class CreateMedicalDocument extends CreateRecord
{
    protected static string $resource = MedicalDocumentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['uploaded_by_user_id'] = auth()->id();
        $data['is_private'] = $data['is_private'] ?? true;

        return $this->fillFileMetadata($data);
    }

    private function fillFileMetadata(array $data): array
    {
        $path = $data['file_path'] ?? null;

        if (! filled($path) || ! Storage::disk('local')->exists($path)) {
            return $data;
        }

        $data['file_name'] = $data['file_name'] ?? basename($path);
        $data['file_mime_type'] = Storage::disk('local')->mimeType($path);
        $data['file_size'] = Storage::disk('local')->size($path);

        return $data;
    }
}
