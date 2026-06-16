<?php

namespace App\Filament\Reception\Resources\Appointments\Pages;

use App\Filament\Reception\Resources\Appointments\AppointmentResource;
use Carbon\Carbon;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditAppointment extends EditRecord
{
    protected static string $resource = AppointmentResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (! empty($data['scheduled_at'])) {
            $scheduledAt = Carbon::parse($data['scheduled_at']);

            $data['appointment_date'] = $scheduledAt->format('Y-m-d');
            $data['selected_time'] = $scheduledAt->format('H:i');
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()->label('Ver cita'),
        ];
    }
}
