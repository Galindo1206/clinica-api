<?php

namespace App\Filament\Resources\MedicalDocuments\Pages;

use App\Filament\Resources\MedicalDocuments\MedicalDocumentResource;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Storage;

class EditMedicalDocument extends EditRecord
{
    protected static string $resource = MedicalDocumentResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $this->fillFileMetadata($data);
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->label('Ver ficha')
                ->icon(Heroicon::OutlinedDocumentMagnifyingGlass),
        ];
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
