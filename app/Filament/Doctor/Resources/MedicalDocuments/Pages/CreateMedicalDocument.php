<?php

namespace App\Filament\Doctor\Resources\MedicalDocuments\Pages;

use App\Filament\Doctor\Resources\MedicalDocuments\MedicalDocumentResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;

class CreateMedicalDocument extends CreateRecord
{
    protected static string $resource = MedicalDocumentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['uploaded_by_user_id'] = auth()->id();
        $data['is_private'] = true;

        if (! empty($data['file_path'])) {
            $path = is_array($data['file_path']) ? reset($data['file_path']) : $data['file_path'];
            $data['file_path'] = $path;
            $data['file_name'] = $data['file_name'] ?? basename($path);

            if (Storage::disk('local')->exists($path)) {
                $data['file_mime_type'] = Storage::disk('local')->mimeType($path);
                $data['file_size'] = Storage::disk('local')->size($path);
            }
        }

        return $data;
    }
}
