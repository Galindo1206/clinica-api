<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogService
{
    public static function record(
        ?int $userId,
        ?int $patientId,
        string $action,
        string $module,
        ?string $description = null,
        ?Request $request = null
    ): void {
        AuditLog::create([
            'user_id' => $userId,
            'patient_id' => $patientId,
            'action' => $action,
            'module' => $module,
            'description' => $description,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
        ]);
    }
}
