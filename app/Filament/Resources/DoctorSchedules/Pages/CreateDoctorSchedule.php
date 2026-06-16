<?php

namespace App\Filament\Resources\DoctorSchedules\Pages;

use App\Filament\Resources\DoctorSchedules\DoctorScheduleResource;
use App\Models\DoctorSchedule;
use Carbon\Carbon;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateDoctorSchedule extends CreateRecord
{
    protected static string $resource = DoctorScheduleResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $startTime = Carbon::parse($data['start_time'])->format('H:i:s');
        $endTime = Carbon::parse($data['end_time'])->format('H:i:s');
        $firstSchedule = null;

        foreach ($data['days'] as $dayOfWeek) {
            $schedule = DoctorSchedule::updateOrCreate(
                [
                    'doctor_id' => $data['doctor_id'],
                    'day_of_week' => (int) $dayOfWeek,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                ],
                [
                    'slot_minutes' => $data['slot_minutes'],
                    'is_active' => $data['is_active'] ?? true,
                ],
            );

            $firstSchedule ??= $schedule;
        }

        return $firstSchedule;
    }
}
