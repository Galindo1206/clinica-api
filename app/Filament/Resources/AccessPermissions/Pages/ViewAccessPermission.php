<?php

namespace App\Filament\Resources\AccessPermissions\Pages;

use App\Filament\Resources\AccessPermissions\AccessPermissionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewAccessPermission extends ViewRecord
{
    protected static string $resource = AccessPermissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->label('Editar permiso')
                ->icon(Heroicon::OutlinedPencilSquare),
        ];
    }
}
