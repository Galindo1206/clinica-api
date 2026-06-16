<?php

namespace App\Filament\Resources\AccessPermissions\Pages;

use App\Filament\Resources\AccessPermissions\AccessPermissionResource;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditAccessPermission extends EditRecord
{
    protected static string $resource = AccessPermissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->label('Ver ficha')
                ->icon(Heroicon::OutlinedDocumentMagnifyingGlass),
        ];
    }
}
