<?php

namespace App\Filament\Resources\AccessPermissions\Pages;

use App\Filament\Resources\AccessPermissions\AccessPermissionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAccessPermission extends CreateRecord
{
    protected static string $resource = AccessPermissionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['granted_by_user_id'] = auth()->id();

        return $data;
    }
}
