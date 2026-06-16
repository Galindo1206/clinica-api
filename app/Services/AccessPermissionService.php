<?php

namespace App\Services;

use App\Models\AccessPermission;
use App\Models\Doctor;
use App\Models\Patient;

class AccessPermissionService
{
    public static function doctorCanAccessPatient(Doctor $doctor, Patient $patient): bool
    {
        return AccessPermission::where('doctor_id', $doctor->id)
            ->where('patient_id', $patient->id)
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>=', now());
            })
            ->exists();
    }
}
