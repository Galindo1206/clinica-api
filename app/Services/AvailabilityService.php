<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Doctor;
use Carbon\Carbon;
use App\Models\DoctorUnavailability;

class AvailabilityService
{
    public static function getDoctorAvailability(Doctor $doctor, string $date): array
    {
        $selectedDate = Carbon::parse($date);

        $dayOfWeek = $selectedDate->dayOfWeek;

        $schedules = $doctor->schedules()
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->get();

        $slots = [];

        foreach ($schedules as $schedule) {
            $start = Carbon::parse($selectedDate->format('Y-m-d') . ' ' . $schedule->start_time);
            $end = Carbon::parse($selectedDate->format('Y-m-d') . ' ' . $schedule->end_time);

            $slotMinutes = $schedule->slot_minutes;

            while ($start->copy()->addMinutes($slotMinutes)->lte($end)) {
                $slotStart = $start->copy();
                $slotEnd = $start->copy()->addMinutes($slotMinutes);

                $hasConflict = Appointment::where('doctor_id', $doctor->id)
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->where(function ($query) use ($slotStart, $slotEnd) {
                        $query->whereBetween('scheduled_at', [$slotStart, $slotEnd->copy()->subSecond()]);
                    })
                    ->exists();

                $isBlocked = DoctorUnavailability::where('doctor_id', $doctor->id)
                    ->where('is_active', true)
                    ->where(function ($query) use ($slotStart, $slotEnd) {
                        $query->where('starts_at', '<', $slotEnd)
                            ->where('ends_at', '>', $slotStart);
                    })
                    ->exists();

                $reason = null;

                if ($hasConflict) {
                    $reason = 'appointment';
                }

                if ($isBlocked) {
                    $reason = 'blocked';
                }

                $slots[] = [
                    'time' => $slotStart->format('H:i'),
                    'datetime' => $slotStart->toDateTimeString(),
                    'available' => ! $hasConflict && ! $isBlocked,
                    'unavailable_reason' => $reason,
                ];

                $start->addMinutes($slotMinutes);
            }
        }

        return [
            'doctor_id' => $doctor->id,
            'doctor_name' => $doctor->user->name,
            'date' => $selectedDate->format('Y-m-d'),
            'slots' => $slots,
        ];
    }
}
