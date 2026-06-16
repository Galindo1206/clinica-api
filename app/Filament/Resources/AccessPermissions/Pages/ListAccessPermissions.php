<?php

namespace App\Filament\Resources\AccessPermissions\Pages;

use App\Filament\Resources\AccessPermissions\AccessPermissionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListAccessPermissions extends ListRecords
{
    protected static string $resource = AccessPermissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Nuevo permiso')
                ->icon(Heroicon::OutlinedKey),
        ];
    }
}
